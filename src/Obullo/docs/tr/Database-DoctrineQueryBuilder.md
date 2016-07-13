
## Doctrine DBAL Sorgu Oluşturucu ( Query Builder )

Doctrine 2.1 sürüm ile gelen query builder sınıfı eklentisi SQL dili için sorgu oluşturmanızı kolaylaştırır. Sorgu oluşturucu bir SQL ifadesine sql bölümleri ekleyen metotlardan ibarettir. Sorgu oluşturucu ile oluşturulan bir SQL ifadesi bir çıktı olarak alınabilir yada <kbd>execute</kbd> metodu ile varolan bağlantı içerisinden sorgu olarak çalıştırılabilir.

<ul>
<li><a href="#methods">Metotlara Erişim</a></li>
<li>
    <a href="#security">Güvenlik</a>
    <ul>
        <li><a href="#sql-injection">SQL Enjeksiyonunu Önlemek</a></li>
    </ul>
</li>

<li>
    <a href="#build-queries">Sorgular Oluşturmak</a>
    <ul>
        <li>
            <a href="#select">SELECT</a>
            <ul>
                <li><a href="#from">$queryBuilder->from()</a></li>
                <li><a href="#get">$queryBuilder->get()</a></li>
                <li><a href="#getSQL">$queryBuilder->getSQL()</a></li>
            </ul>
        </li>
        <li>
            <a href="#where">WHERE</a>
            <ul>
                <li><a href="#andWhere">$queryBuilder->andWhere()</a></li>
                <li><a href="#orWhere">$queryBuilder->orWhere()</a></li>
                <li><a href="#where-expr">$queryBuilder->expr()</a></li>
            </ul>
        </li>

        <li>
            <a href="#groupBy">GROUP BY & HAVING</a>
            <ul>
                <li><a href="#orHaving">$queryBuilder->orHaving()</a></li>
                <li><a href="#andHaving">$queryBuilder->andHaving()</a></li>
                <li><a href="#addGroupBy">$queryBuilder->addGroupBy()</a></li>
            </ul>
        </li>

        <li>
            <a href="#join">JOIN</a>
            <ul>
                <li><a href="#innerJoin">$queryBuilder->innerJoin()</a></li>
                <li><a href="#rightJoin">$queryBuilder->rightJoin()</a></li>
                <li><a href="#leftJoin">$queryBuilder->leftJoin()</a></li>
            </ul>
        </li>

        <li><a href="#order-by">ORDER BY</a></li>
        <li><a href="#limit">LIMIT</a></li>
        <li><a href="#values">VALUES</a></li>
        <li><a href="#set">SET</a></li>
        <li>
            <a href="#operators">Sql Operatörleri</a>
            <ul>
                <li><a href="#expr">$queryBuilder->expr()</a></li>
                <li><a href="#andx">$queryBuilder->andx()</a></li>
                <li><a href="#orx">$queryBuilder->orx()</a></li>
                <li><a href="#eq">$queryBuilder->eq()</a></li>
                <li><a href="#neq">$queryBuilder->neq()</a></li>
                <li><a href="#lt">$queryBuilder->lt()</a></li>
                <li><a href="#lte">$queryBuilder->lte()</a></li>
                <li><a href="#gt">$queryBuilder->gt()</a></li>
                <li><a href="#gte">$queryBuilder->gte()</a></li>
                <li><a href="#isNull">$queryBuilder->isNull()</a></li>
                <li><a href="#isNotNull">$queryBuilder->isNotNull()</a></li>
                <li><a href="#notLike">$queryBuilder->notLike()</a></li>
                <li><a href="#in">$queryBuilder->in()</a></li>
                <li><a href="#notIn">$queryBuilder->notIn()</a></li>
                <li><a href="#escape">$queryBuilder->escape()</a></li>
            </ul>
        </li>
        <li><a href="#query-binding">Sorgulara Parametre Yerleştirme ( Query Binding )</a></li>
    </ul>
</li>

</ul>

<a name="methods"></a>

### Metotlara Erişim 

Servis sağlayıcısı yapılandırmasından sonra sorgu oluşturucuyu servis sağlayıcısı üzerinden bağlantı parametereleri göndererek oluşturabilirsiniz.

Sınıfı servis sağlayıcısı ile bir kez oluşturduktan sonra istediğiniz değişkene atayabilirsiniz.

```php
$queryBuilder = $this->database->shared()->createQueryBuilder();

$row = $queryBuilder
    ->select('id', 'name')
    ->get('users')
    ->row();
```
or

```php
$this->db = $this->database->shared()->createQueryBuilder();

$row = $this->db
    ->select('id', 'name')
    ->get('users')
    ->row();
```

<a name="security"></a>

### Güvenlik

Veri tabanı operasyonlarında güvenli sorgular oluşturmak herhangi bir saldırı riskini önler. Veritabanı operasyonlarında bilinen en tehlikeli saldırı yöntemi <a href="http://tr.wikipedia.org/wiki/SQL_Injection" target="_blank">SQL Enjeksiyonu</a> dur.

<a name="sql-injection"></a>

#### SQL Enjeksiyonunu Önlemek

Sorgu oluşturucunun SQL ataklarını nasıl ve hangi şartlara göre önlediğini anlamak önemlidir. Son kullanıcıdan gelen tüm girdilerin SQL enjeksiyon riski vardır. Sorgu oluşturucu ile güvenli çalışmak için <b>asla</b> <kbd>setParameter()</kbd> metodu dışındaki metotlara kullanıcı girdilerini göndermeyin. Ve <kbd>setParameter($placeholder, $value)</kbd> metodunu kullandığınızda placeholder <kbd>?</kbd> veya <kbd>:name</kbd> söz dizimlerinden birini metod ile birlikte kullanın.

Aşağıdaki örnekte placeholder <kbd>?</kbd> parametre yerleştirme yöntemi ile güvenli bir sorgu oluşturuluyor.

```php
$email = "me@example.com";

$row = $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ?')
    ->setParameter(0, $email)
    ->execute()
    ->row();
```

API tasarımındaki sayısal değerlere ilişkin olarak QueryBuilder uygulama arayüzü PDO arayüzünden farklı olarak <kbd>1</kbd> yerine <kbd>0</kbd> değeri ile başlar.

<a name="build-queries"></a>
<a name="select"></a>
<a name="from"></a>

### Sorgular Oluşturmak

Sorgu oluşturucu SELECT, INSERT, UPDATE ve DELETE sorgularını destekler. Select sorguları <kbd>select()</kbd> metodu ile INSERT, UPDATE ve DELETE sorguları ise tablo ismi girilerek <kbd>insert($table)</kbd>, <kbd>update($table)</kbd> ve <kbd>delete($table)</kbd> metotları ile oluşturulur.

#### SELECT

Select metodu kullanılırken tablo seçmek için from metodu kullanılır sorgu execute metodu ile çalıştırılır. Sorgu sonuçları için <kbd>Obullo\Database\Doctrine\DBAL\Result</kbd> sınıfı içerisindeki count(), row(), rowArray(), result() ve resultArray() metotları kullanılır.

##### $queryBuilder->from()

```php
$row = $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->execute()
    ->row();
```

Opsiyonel olarak <kbd>from()</kbd> metodu ikinci parametresine bir tablo takma adı gönderilebilir.

```php
$queryBuilder
    ->select('u.id', 'u.name')
    ->from('users', 'u')
    ->where('u.email = ?')
    ->execute()
```

<a name="get"></a>

##### $queryBuilder->get()

Opsiyonel olarak bazı durumlarda <kbd>execute()</kbd> metodu yerine Obullo adaptörü içerisinde bulunan <kbd>get()</kbd> kısayolunu da kullabilirsiniz.

```php
$row = $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->execute()
    ->row();
```
<a name="getSQL"></a>

##### $queryBuilder->getSQL()

Eğer sorgu çalıştırılmak istenmiyorsa sorgu çıktısı <kbd>getSQL()</kbd> metodu ile alınabilir.

```php
echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->getSQL();

// SELECT id, name FROM users
```

<a name="where"></a>

#### WHERE

SELECT, UPDATE ve DELETE türündeki sorgularda aşağıdaki gibi <kbd>where()</kbd> ifadesi kullanabilirsiniz.

```php
$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ?')
    ->execute();
```

<a name="andWhere"></a>

##### $queryBuilder->andWhere()

Birbiri ardına kullanıldığında where ifadesinden sonra AND ifadelerini oluşturur.

```php
$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->andWhere('email = ?');
    ->andWhere('username = ?')
    ->execute();
```

<a name="orWhere"></a>

##### $queryBuilder->orWhere()

Birbiri ardına kullanıldığında where ifadesinden sonra OR ifadelerini oluşturur.

```php
$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->orWhere('email = ?');
    ->orWhere('username = ?')
    ->execute();
```

<a name="where-expr"></a>

##### $queryBuilder->expr()

Alternatif olarak where ifadeleri içerisinde ifadeler yaratmak için <kbd>$queryBuilder->expr()</kbd> metodunu kullabilirsiniz.

```php
$e = $queryBuilder->expr();

$or = $e->orx();
$or->add($e->eq('u.id', 1));
$or->add($e->eq('u.id', 2));

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($or);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE (u.id = 1) OR (u.id = 2)
```

<a name="groupBy"></a>

#### GROUP BY & HAVING

SELECT ifadesi GROUP BY ve HAVING ifadeleri ile kullanılabilir. Having metodu kullanma işlevi where() metodu işlevi gibi çalışır ve andHaving() veya orHaving() gibi size uyan metotlardan biri ile kombine edilebilir.

```php
echo $queryBuilder
    ->select('DATE(last_login) as date', 'COUNT(id) AS users')
    ->from('users')
    ->groupBy('DATE(last_login)')
    ->having('users > 10');

// SELECT DATE(last_login) as date, COUNT(id) AS users 
// FROM users GROUP BY DATE(last_login) HAVING users > 10 
```

<a name="orHaving"></a>

##### $queryBuilder->orHaving()

```php
echo $queryBuilder
    ->select('COUNT(id) as users')
    ->from('users')
    ->groupBy('username')
    ->having('users > 10')
    ->orHaving('users < 5');

// SELECT COUNT(id) as users FROM users GROUP BY username 
// HAVING (users > 10) OR (users < 5) 
```

<a name="andHaving"></a>

##### $queryBuilder->andHaving()

```php
echo $queryBuilder
    ->select('COUNT(id) as users')
    ->from('users')
    ->groupBy('username')
    ->having('users > 10')
    ->andHaving("username = 'test'");

// SELECT COUNT(id) as users FROM users GROUP BY username HAVING (users > 10) AND (username = 'test')
```

<a name="addGroupBy"></a>

##### $queryBuilder->addGroupBy()

GROUP BY ifadesi için groupBy() metodu ile en son groupBy değeri değiştirilebilir yada addGroupBy() metodu ile birden fazla groupBy eklenebilir.

```php
echo $queryBuilder
    ->select('u.name')
     ->from('users', 'u')
     ->groupBy('u.lastLogin')
     ->addGroupBy('u.createdAt');

// SELECT u.name FROM users u GROUP BY u.lastLogin, u.createdAt 
```

<a name="join"></a>

#### JOIN

SELECT ifadeleri için INNER, LEFT ve RIGHT olmak üzere farklı türde JOIN ifadeleri oluşturabilirsiniz: RIGHT join tüm platformlarda desteklenmeyebilir mesela Sqlite veritabanında RIGHT join desteklemez. Bir JOIN ifadesi daima bir FROM ifadesinin bir parçasıdır bu yüzden FROM parçasında ona verilen bir takma ad join metodunda ilk parametre olarak girilir. İkinci ve üçüncü parametrelere join tablosunun adı ve kısa takmaadı girilir, dördüncü parametre ise ON ifadesine ait eşleştirmeyi içerir.

```php
echo $queryBuilder
    ->select('u.id', 'u.name', 'p.number')
    ->from('users', 'u')
    ->innerJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');

// SELECT u.id, u.name, p.number FROM users u LEFT JOIN phonenumbers p ON u.id = p.user_id 
```

join(), innerJoin(), leftJoin() ve rightJoin() fonksiyonları için işlevler aynıdır join() metodu innerJoin() metodunun takma adı dır.


<a name="innerJoin"></a>

##### $queryBuilder->innerJoin()

```php
echo $queryBuilder
    ->select('u.id', 'u.name', 'p.number')
    ->from('users', 'u')
    ->innerJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');

// SELECT u.id, u.name, p.number FROM users u INNER JOIN phonenumbers p ON u.id = p.user_id
```

<a name="rightJoin"></a>

##### $queryBuilder->rightJoin()

```php
echo $queryBuilder
    ->select('u.id', 'u.name', 'p.number')
    ->from('users', 'u')
    ->rightJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');

// SELECT u.id, u.name, p.number FROM users u RIGHT JOIN phonenumbers p ON u.id = p.user_id 
```

<a name="leftJoin"></a>

##### $queryBuilder->leftJoin()

```php
echo $queryBuilder
    ->select('u.id', 'u.name', 'p.number')
    ->from('users', 'u')
    ->leftJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');

// SELECT u.id, u.name, p.number FROM users u LEFT JOIN phonenumbers p ON u.id = p.user_id
```

<a name="order-by"></a>

#### ORDER BY

orderBy($sort, $order = null) metodu ORDER BY ifadesine sıralama ifadeleri ekler. orderBy metoduna ait olan değeri değiştirmemek için tekrar orderBy yerine addOrderBy metodu kullanılır.

```php
echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->orderBy('username', 'ASC')
    ->addOrderBy('last_login', 'ASC NULLS FIRST');

// SELECT id, name FROM users ORDER BY username ASC, last_login ASC NULLS FIRST
```

Opsiyonel parametre olan $order parametresi herhangi bir güvenlik önlemi içermez, bu parametreye girilen kullanıcı girdileri tehlikeli olabilir bu nedenle bu girdide sadece SQL ifadelerine izin verilmelidir.

<a name="limit"></a>

#### LIMIT ve OFFSET

Yalnızca bir kaç veritabanı LIMIT ifadesini destekler MySQL veritabanı bunların arasındadır fakat Doctrine DBAL arayüzü bu fonsiyonaliteyi tüm veritabanı sürücüleri için destekler. Bu özellikleri kullanabilmek için <kbd>setFirstResult($offset)</kbd> ve <kbd>setMaxResult($limit)</kbd> metotlarını kullanabilirsiniz.

```php
echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->setFirstResult(10);
    ->setMaxResult(20)

// SELECT id, name FROM users LIMIT 20 OFFSET 10 
```

<a name="values"></a>

#### VALUES

Sorgu oluşturucu içerisindeki values() metodu ile INSERT ifadeleri için sütunlara özgü değerler sorguya gönderilebilir.

```php
echo $queryBuilder
    ->insert('users')
    ->values(
        array(
            'name' => '?',
            'password' => '?'
        )
    )
    ->setParameter(0, $username)
    ->setParameter(1, $password);

// INSERT INTO users (name, password) VALUES (?, ?)
```

values() yerine setValue() metodu kullanılabilir,

```php
echo $queryBuilder
    ->insert('users')
    ->setValue('name', '?')
    ->setValue('password', '?')
    ->setParameter(0, $username)
    ->setParameter(1, $password)
;
// INSERT INTO users (name, password) VALUES (?, ?)
```

yada her iki method birlikte de kombine edilebilir.

```php
echo $queryBuilder
    ->insert('users')
    ->values(
        array(
            'name' => '?'
        )
    )
    ->setParameter(0, $username)
;
// INSERT INTO users (name) VALUES (?)

if ($password) {
    $queryBuilder
        ->setValue('password', '?')
        ->setParameter(1, $password)
    ;
    // INSERT INTO users (name, password) VALUES (?, ?)
}
```

Eğer herhangi bir değer atanmazsa sonuç boş bir sql sorgusuna döner.

```php
echo $queryBuilder
    ->insert('users');

// INSERT INTO users () VALUES ()
```

Sorgunun çalışabilmesi için <kbd>execute()</kbd> metodunun en sonda çağrılması gerekir.

```php
echo $queryBuilder->insert('users')
    ->values(['username' => '?'])
    ->setParameter(0, 'user@example.com')
    ->execute();

// 1
```

<a name="set"></a>

#### SET

UPDATE ifadesi için sütün değerleri göndermek zorunludur ve bu değerler <kbd>set()</kbd> fonksiyonu ile gönderilir. İkinci parametre için dikkatli olun bu parametreye kullanıcı girdilerini direkt göndermek güvenli değildir.

```php
echo $queryBuilder
    ->update('users', 'u')
    ->set('u.logins', 'u.logins + 1')
    ->set('u.last_login', '?')
    ->setParameter(0, $userInputLastLogin);

// UPDATE users u SET u.logins = u.logins + 1, u.last_login = ? 
```

<a name="operators"></a>

### Sql Operatörleri

Daha fazla kompleks WHERE, HAVING veya diğer ifadeler için operatörler ile bütün bir sorgu içerisinde sorgu parçaları yaratabilirsiniz. Bunun için sorgu oluşturucuda önce <kbd>$queryBuilder->expr()</kbd> metodu ile ifade nesnesini yaratmanız gerekir ve sonra oluşan ifade nesnesi üzerinden ilgili metotlara ulaşabilirsiniz.

Genellikle ifade oluşturmak için ilk önce And veya Or operatörleri seçilir. Daha sonra seçilen operatör içerisinde karşılaştırma operatörü belirtilerek sorgu parçaları oluşturulur.

```php
$e = $queryBuilder->expr();

echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where(
        $e->andX(
            $e->eq('username', '?'),
            $e->eq('email', '?')
        )
    );

// SELECT id, name FROM users WHERE (username = ?) AND (email = ?) 
```

andX() ve orX() metotları isteğe bağlı sayıda alt alta ilişkili argüman alabilirler.

<a name="expr"></a>

##### $queryBuilder->expr()

Operatör metotlarına ulaşmak için ifade nesnesini oluşturur.

```php
$e = $queryBuilder->expr();

echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where(
        $e->andX(
            $e->eq('username', '?'),
            $e->eq('email', '?')
        )
    );

// SELECT id, name FROM users WHERE (username = ?) AND (email = ?) 
```

<a name="andx"></a>

##### $queryBuilder->andx()

Girilen ifadelere göre AND bağlacı yaratır.


```php
$e = $queryBuilder->expr();

echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where(
        $e->andX(
            $e->eq('username', '?'),
            $e->eq('email', '?')
        )
    );

// SELECT id, name FROM users WHERE (username = ?) AND (email = ?)
```

<a name="orx"></a>

##### $queryBuilder->orx()

Girilen ifadelere göre OR bağlacı yaratır.

```php
$e = $queryBuilder->expr();

$or = $e->orx();
$or->add($e->eq('u.id', 1));
$or->add($e->eq('u.id', 2));

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($or);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE (u.id = 1) OR (u.id = 2)     
```

<a name="eq"></a>

##### $queryBuilder->eq()

Girilen argümanlar ile bir eşitlik kıyaslama ifadesi oluşturur.

```php
$e = $queryBuilder->expr();

$or = $e->orx();
$or->add($e->eq('u.id', 1));
$or->add($e->eq('u.id', 2));

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($or);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE (u.id = 1) OR (u.id = 2) 
```

<a name="neq"></a>

##### $queryBuilder->neq()

Girilen argümanlar ile bir eşitlik tersi kıyaslama ifadesi oluşturur.

```php
$e = $queryBuilder->expr();

$or = $e->orx();
$or->add($e->neq('u.id', 2));

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($or);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE u.id <> 2 
```

<a name="lt"></a>

##### $queryBuilder->lt()

Girilen argümanlar ile bir küçüktür kıyaslama ifadesi oluşturur.

```php
$e = this->db->expr();

$and = $e->andx();
$and = $e->lt('u.id', 49);

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->>where($and);
```

<a name="lte"></a>

##### $queryBuilder->lte()

Girilen argümanlar ile bir küçük eşit kıyaslama ifadesi oluşturur.

```php
$e = $queryBuilder->expr();

$and = $e->andx();
$and = $e->lte('u.id', 49);

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($and);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE u.id <= 49
```

<a name="gt"></a>


##### $queryBuilder->gt()

Girilen argümanlar ile bir büyüktür kıyaslama ifadesi oluşturur.

```php
$e = $queryBuilder->expr();

$and = $e->andx();
$and = $e->gt('u.id', 10);

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($and);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE u.id > 10 
```

<a name="gte"></a>

##### $queryBuilder->gte()

Girilen argümanlar ile bir büyük eşit kıyaslama ifadesi oluşturur.

```php
$e = $queryBuilder->expr();

$and = $e->andx();
$and = $e->gte('u.id', 10);

echo $queryBuilder->update('users', 'u')
    ->set('u.password', md5('password'))
    ->where($and);

// UPDATE users u SET u.password = 5f4dcc3b5aa765d61d8327deb882cf99 WHERE u.id >= 10
```

<a name="isNull"></a>

##### $queryBuilder->isNull($str)

Girilen argümanlar ile bir IS NULL ifadesi oluşturur.

<a name="isNotNull"></a>

##### $queryBuilder->isNotNull($str)

Girilen argümanlar ile bir IS NOT NULL ifadesi oluşturur.

<a name="like"></a>

##### $queryBuilder->like($field, $val)

Girilen argümanlar ile bir LIKE() karşılaştırma ifadesi oluşturur.

<a name="notLike"></a>

##### $queryBuilder->notLike($field, $val)

Girilen argümanlar ile bir NOT LIKE() karşılaştırma ifadesi oluşturur.

<a name="in"></a>

##### $queryBuilder->in($field, mixed $vals)

Girilen argümanlar ile bir IN() karşılaştırma ifadesi oluşturur.

<a name="notIn"></a>

##### $queryBuilder->notIn($field, mixed $vals)

Girilen argümanlar ile bir NOT IN() karşılaştırma ifadesi oluşturur.

<a name="escape"></a>

##### $queryBuilder->escape(string|array $input)

Sql enjeksiyon tehdidini önlemek için girilen kullanıcı girdisindeki tehlikeli karakterleri kaçış sembolü ile farklılaştırır. Eğer kullanıcı girdilerinde parametre yerleştirme kullanıyorsanız bu fonksiyonu kullanmanıza gerek kalmaz çünkü escape davranışı böyle bir durumda kendiliğinden sorgu oluşturucu sınıfı içerisinde yapılır.

<a name="query-binding"></a>

### Sorgulara Parametre Yerleştirme ( Query Binding )

Genellikle parametre yerleştirme isimleri ( placeholder names ) bir kesinlik ifade etmezler yani <kbd>:value</kbd> yada <kbd>?</kbd> şeklindedirler. Eğer tam bir esneklik isteniyorsa aşağıdaki farklı parametre yerleştirme metotlarını sorgularınız içerisinde kullanabilirsiniz.

```php
$id = 5;
$email = 'user@example.com';

echo $queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ' .  $queryBuilder->createNamedParameter($email))
    ->andWhere('id = ' .  $queryBuilder->createNamedParameter($id));

// SELECT id, name FROM users WHERE (email = :dcValue1) AND (id = :dcValue2)
```

Soru işareti kullanılarak parametre yerleştirmeye bir örnek:

```php
$email = 'user@example.com';

$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ' . $queryBuilder->createPositionalParameter($email));

// SELECT id, name FROM users WHERE email = ?
```