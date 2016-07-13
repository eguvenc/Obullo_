
## Doctrine Veritabanı Katmanı ( DBAL )

Doctrine katmanı standart PDO programlama arayüzü üzerinden benzer arabirimler kullanmak yoluyla gerçek PDO API sürücüleri gibi yerli veya el yapımı API'leri veya özel sürücüleri çalıştırmayı mümkün kılar.

<ul>
<li>
    <a href="#server-requirements">Sunucu Gereksinimleri</a>
    <ul>
        <li><a href="#supported-databases">Desteklenen Veritabanları</a></li>
    </ul>
</li>

<li>
    <a href="#database-connection">Veritabanı Bağlantısı</a>
    <ul>
        <li><a href="#standart-connection">Standart Bağlantı</a></li>
        <li><a href="#unix-connection">Unix Soket Bağlantısı</a></li>
        <li><a href="#connection-management">Bağlantı Yönetimi</a></li>
    </ul>
</li>

<li>
    <a href="#service-provider">Servis Sağlayıcısı</a>
    <ul>
        <li><a href="#getting-existing-connection">Varolan Bağlantıyı Almak</a></li>
        <li><a href="#creating-new-connection">Yeni Bir Bağlantı Oluşturmak</a></li>
    </ul>
</li>

<li>
    <a href="#reading-database">Veritabanından Okumak</a>
    <ul>
        <li><a href="#query">$this->db->query()</a></li>
        <li><a href="#executeQuery">$this->db->executeQuery()</a></li>
    </ul>
</li>

<li>
    <a href="#generating-results">Veritabanından Sonuçlar Getirmek</a>
    <ul>
        <li><a href="#count">$this->db->count()</a></li>
        <li><a href="#row">$this->db->row()</a></li>
        <li><a href="#rowArray">$this->db->rowArray()</a></li>
        <li><a href="#result">$this->db->result()</a></li>
        <li><a href="#resultArray">$this->db->resultArray()</a></li>
        <li><a href="#resultFail">Başarısız İşlem Sonucu</a></li>
    </ul>
</li>

<li>
    <a href="#writing-database">Veritabanına Yazmak</a>
    <ul>    
        <li><a href="#insert">$this->db->insert()</a></li>
        <li><a href="#update">$this->db->update()</a></li>
        <li><a href="#delete">$this->db->delete()</a></li>
        <li><a href="#executeUpdate">$this->db->executeUpdate()</a></li>
    </ul>
</li>

<li>
    <a href="#security">Güvenlik</a>
    <ul>
        <li><a href="#escaping-sql-injections">Sql Enjeksiyonunu Önlemek</a></li>
        <li><a href="#prepare">$this->db->prepare()</a></li>
        <li><a href="#bindValue">$this->db->bindValue()</a></li>
        <li><a href="#bindParam">$this->db->bindParam()</a></li>
        <li><a href="#execute">$this->db->execute()</a></li>
        <li><a href="#escape">$this->db->escape()</a></li>
    </ul>
</li>

<li>
    <a href="#escaping-sql-injections">Sql Enjeksiyonundan Kaçış</a>
    <ul>
        <li><a href="#escape">$this->db->escape()</a></li>
    </ul>
</li>

<li>
    <a href="#transactions">Transaksiyonlar</a>
    <ul>
        <li><a href="#auto-transaction">Otomatik Transaksiyon</a></li>
        <li><a href="#native-transaction">Doğal Transaksiyon</a></li>
    </ul>
</li>

<li>
    <a href="#helper-functions">Yardımcı Fonksiyonlar</a>
    <ul>
        <li><a href="#connect">$this->db->connect()</a></li>
        <li><a href="#getDrivers">$this->db->getDrivers()</a></li>
        <li><a href="#getConnection">$this->db->getConnection()</a></li>
        <li><a href="#getStmt">$this->db->getStmt()</a></li>
        <li><a href="#inTransaction">$this->db->inTransaction()</a></li>
        <li><a href="#lastInsertId">$this->db->lastInsertId()</a></li>
        <li><a href="#quoteIdentifier">$this->db->quoteIdentifier()</a></li>
    </ul>
</li>

<li>
    <a href="#additionalFeatures">Ek Özellikler</a>
    <ul>
        <li><a href="#queryBuilder">Sorgu Oluşturucu ( Query Builder )</a></li>
    </ul>
</li>

</ul>

<a name='server-requirements'></a>

### Sunucu Gereksinimleri

<a name='supported-databases'></a>

#### Desteklenen Veritabanları

<table class="span9">
<thead>
<tr>
<th>Veritabanı</th>
<th>Bağlantı Adları</th>
<th>Bağlantı Dizesi</th>
</tr>
</thead>
<tbody>

<tr>
<td>MySQL 3.x/4.x/5.x</td>
<td>pdo_mysql, mysql, mysqli, mysql2 ( Amazon RDS )</td>
<td>pdo_mysql:host=localhost;port=;dbname=example;charset=UTF-8</td>
</tr>

<tr>
<td>Oracle</td>
<td>pdo_oci, oci8</td>
<td>pdo_oci:host=localhost;port=4486;dbname=example</td>
</tr>

<tr>
<td>Microsoft SQL Server</td>
<td>pdo_sqlsrv, sqlsrv, mssql</td>
<td>pdo_sqlsrv:host=localhost;port=;dbname=example</td>
</tr>

<tr>
<td>PostgreSQL</td>
<td>pdo_pgsql, pgsql, postgres, postgresql</td>
<td>pdo_pgsql:host=localhost;port=;dbname=example</td>
</tr>

<tr>
<td>SAP Sybase SQL Anywhere</td>
<td>sqlanywhere</td>
<td>sqlanywhere:host=localhost;port=;dbname=example;persistent=1;</td>
</tr>

<tr>
<td>SQLite</td>
<td>pdo_sqlite, sqlite, sqlite3</td>
<td>sqlite:dbname=/usr/local/var/db.sqlite</td>
</tr>

<tr>
<td>SQLite Memory</td>
<td>pdo_sqlite, sqlite, sqlite3</td>
<td>sqlite:dbname=:memory:</td>
</tr>

<tr>
<td>Drizzle</td>
<td>drizzle_pdo_mysql</td>
<td>drizzle_pdo_mysql:host=localhost;dbname=example;</td>
</tr>

</tbody>
</table>

Ekstra bağlantı parametrelerini <a href="http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html">bu linkten</a> elde edebilirsiniz.


<a name='database-connection'></a>

### Veritabanı Bağlantısı

Veritabanı ile bağlantı kurulması veritabanı metotları ( query, execute, transaction .. ) kullanıldığı zaman gerçekleşir. Bu metotların kullanılmadığı yerlerde bağlantı açık değildir ve bir kere açılan bir bağlantı varsa bu bağlantı tekrar açılmaz. Veritabanı sınıfı <kbd>db</kbd> servisi tarafından yönetilir ve <kbd>db</kbd> servisi de bağlantı yönetimi için <kbd>database</kbd> servis sağlayıcısını kullanır.
<a name='standart-connection'></a>

#### Standart Bağlantı

Veritabanına bağlantı konfigürasyonu <kbd>providers/database.php</kbd> dosyasından gerçekleştirilir. Aşağıdaki örnek bağlantı şeması <kbd>dsn</kbd> anahtarına girilir.

```php
pdo_mysql:host=localhost;port=;dbname=test;
```

<a name='unix-connection'></a>

#### Unix Socket Bağlantısı

Unix soket tipinde bağlantı isteniyorsa bağlantı şeması aşağıdaki gibi olmalıdır.

```php
pdo_mysql:unix_socket=/PATH/TO/SOCK_FILE;dbname=YOUR_DB_NAME;charset=utf8;
```
Örnek bir bağlantı

```php
pdo_mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=test
```

<a name='getting-existing-connection'></a>

##### Varolan Bağlantıyı Almak

Servis sağlayıcısından var olan paylaşımlı bir bağlantıyı almak için aşağıdaki yöntem izlenir.

```php
$db = $container->get('database')->shared(['connection' => 'default']);
$db->method();
```

<a name='creating-new-connection'></a>

##### Yeni Bir Bağlantı Oluşturmak

Eğer <kbd>db</kbd> servisinin kullandığı veritabanı nesnesi dışında <kbd>tanımsız</kbd> olan yeni bir veritabanı bağlantısına ihtiyaç duyuyorsanız bunun için servis sağlayıcısı <kbd>factory</kbd> metodunu kullanır.

```php
$db = $container->get('database')->factory(
    [
        'dsn'      => 'pdo_mysql:host=localhost;port=;dbname=test',
        'username' => 'root',
        'password' => '123456',
        'options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    ]
);
```

<a name='service-provider'></a>

#### Servis Sağlayıcısı

Database servis sağlayıcısı <kbd>default</kbd> bağlantısıyla yaratılan veritabanına ait metotlara kısayoldan ulaşmanızı sağlar. Servis sağlayıcının çalışabilmesi için aşağıdaki gibi tanımlı olması gerekir.

```php
$container->addServiceProvider('App\ServiceProvider\Database');
```

```php 
$container->share('database', 'Obullo\Container\Connector\DoctrineDBAL')
     ->withArgument($container)
     ->withArgument($params);
```

Veritabanı metotlarını çağırmak,


```php
$this->db = $this->database->shared();
$this->db->query(" .. ");
```

<a name='reading-database'></a>

### Veritabanından Okumak

<a name='query'></a>

##### $this->db->query($sql)

Bir sql sorgusunu çalıştırır.

```php
$users = $this->db->query("SELECT * FROM users")->resultArray();
```

<a name='executeQuery'></a>

##### $this->db->executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null )

Hazırlanmış bir sql ifadesine gönderilen parametreleri yerleştirerek sorguyu çalıştırır.

```php
$this->db->executeQuery('SELECT * FROM user WHERE username = ?', ['eagle'])->row();
```

Array türündeki paremetrelerin kullanıma bir örnek

```php
$this->db->executeQuery('SELECT * FROM articles WHERE id IN (?)',
    array([1, 2, 3, 4, 5, 6]),
    [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
);
```

Doctrine için Tanımlı Opsiyonel Array Parametere Türleri

* \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
* \Doctrine\DBAL\Connection::PARAM_INT_ARRAY

Aynı sorgunun Doctrine\DBAL\Connection::PARAM_INT_ARRAY olmadan kullanımı

```php
$this->db->executeQuery('SELECT * FROM articles WHERE id IN (?, ?, ?, ?, ?, ?)',
    array(1, 2, 3, 4, 5, 6),
    [
        \PDO::PARAM_INT,
        \PDO::PARAM_INT,
        \PDO::PARAM_INT,
        \PDO::PARAM_INT,
        \PDO::PARAM_INT,
        \PDO::PARAM_INT
    ]
);
```

<a name='generating-results'></a>

### Veritabanından Sonuçlar Getirmek

Veritabanına yapılan sorgudan sonra aşağıdaki metotlardan biri seçilerek sonuçlar elde edilir.

<a name='count'></a>

##### $this->db->count()

Son sql sorgusundan etkilenen satır sayısını döndürür.

```php
echo $this->db->query("SELECT * FROM users")->count();  // 5
```

<a name='row'></a>

##### $this->db->row($default = false)

Son sql sorgusundan dönen tekil sonucu <kbd>nesne</kbd> türünden verir.

```php
$row = $this->db->query("SELECT * FROM users WHERE id = 2")->row();
```

```php
var_dump($row);
```

```php
stdClass Object
(
    [id] => 2
    [username] => user@example.com
)
```

<a name='rowArray'></a>

##### $this->db->rowArray($default = false)

Son sql sorgusundan dönen tekil sonucu <kbd>dizi</kbd> türünden verir.

```php
$row = $this->db->query("SELECT * FROM users WHERE id = 2")->rowArray();
```

```php
var_dump($row);
```

```php
Array
(
    [id] => 2
    [username] => user@example.com
)
```

<a name='result'></a>

##### $this->db->result($default = false)

Son sql sorgusundan dönen tüm sonuçları bir dizi içinde <kbd>nesne</kbd> türünden verir.

```php
$results = $this->db->query("SELECT id, username FROM users")->result();
```

```php
var_dump($results);
```

```php
Array
(
    [0] => stdClass Object
        (
            [id] => 1
            [username] => user@example.com
        )

    [1] => stdClass Object
        (
            [id] => 2
            [username] => user2@example.com
        )

)
```

<a name='resultArray'></a>

##### $this->db->resultArray($default = false)

Son sql sorgusundan dönen tüm sonuçları <kbd>dizi</kbd> türünden verir.

```php
$results = $this->db->query("SELECT id, username FROM users")->resultArray();
```

```php
var_dump($results);
```

```php
Array
(
    [0] => Array
        (
            [id] => 1
            [username] => user@gmail.com
        )

    [1] => Array
        (
            [id] => 2
            [username] => user2@example.com
        )

)
```

<a name='resultFail'></a>

#### Başarısız İşlem Sonucu

Eğer sonuç metotlarına ilk parametre gönderilirse sonuçların başarısız olması durumunda fonksiyonun hangi türe döneceği belirlenir. Eğer başarısız işlemde sonucunun <kbd>array()</kbd> değerine dönmesini isteseydik fonksiyonu aşağıdaki gibi kullanmalıydık.

```php
$row = $this->db->query("SELECT * FROM users WHERE id = 748")
    ->resultArray(array());
```

```php
var_dump($row);  // Çıktı array(0) { } 
```

<a name='writing-database'></a>

### Veritabanına Yazmak

Veritabanına yazma işlemleri aşağıdaki metotlar yardımı ile yapılır. Bir yazma metodu çalıştırıldıktan sonra veri işleme gerçekleşir ve metot etkilenen satır sayısına geri döner ve etkilenen satırları alabilmek ayrıca sütun sayma işlemi yapmaya gerek kalmaz.

<a name='insert'></a>

##### $this->db->insert($table, array $data, array $types = array())

```php
$count = $this->db->insert(
    'users', 
    ['username' => 'test@example.com', 'password' => 123456], 
    ['username' => \PDO::PARAM_STR, 'password' => \PDO::PARAM_INT]
);

// INSERT INTO user (username, password) VALUES (?, ?)
// INSERT INTO user (username, password) VALUES ('test@example.com', 123456)
```

<a name='update'></a>

##### $this->db->update($table, array $data, array $identifier, array $types = array())

```php
$count = $this->db->update(
    'users', 
    ['password' => '123456', 'username' => 'user@example.com'], 
    ['id' => 1], 
    [
        'id' => \PDO::PARAM_INT,
        'username' => \PDO::PARAM_STR,
        'password' => \PDO::PARAM_STR
    ]
);

// UPDATE users SET password = ?, username = ? WHERE id = ?
// UPDATE users SET password = '123456', username = 'user@example.com' WHERE id = 1
```

Update operasyonunda eğer veritabanındaki değer gönderilen değer ile <kbd>aynı</kbd> ise update işlemi yapılmaz ve etkilenen satır sayısı <kbd>0</kbd> olarak elde edilir.

<a name='delete'></a>

##### $this->db->delete($table, array $identifier, array $types = array())

```php
$count = $this->db->delete('users', ['id' => 18], ['id' => \PDO::PARAM_INT]);

// DELETE FROM users WHERE id = ?
// DELETE FROM users WHERE id = 18
```

<a name='executeUpdate'></a>

##### $this->db->executeUpdate($query, array $params = array(), array $types = array()

Hazırlanmış bir veritabanına yazma sorgusunu (insert, update, delete) gönderilen parametreler ile çalıştırıp etkilenen satır sayısına geri döner.

```php
$count = $this->db->executeUpdate(
    'UPDATE user SET username = ? WHERE id = ?',
    ['neo', 1],
    [\PDO::PARAM_STR, \PDO::PARAM_INT]
);
```

###### Etkilenen Satır Sayısı

Yukarıdaki operasyonların herhangi birinde çıktı ekrana yazdırıldığında etkilenen satır sayısı elde edilir.

```php
var_dump($count);
```

```php
int(1)
```

<a name='security'></a>

### Güvenlik

<a name='escaping-sql-injections'></a>

#### Sql Enjeksiyonunu Önlemek

Pdo nesnesi ile güvenli sorgular oluşturmak için <a href="http://php.net/manual/tr/pdo.prepare.php" target="_blank">prepare</a> ve <a href="http://php.net/manual/tr/pdostatement.execute.php" target="_blank">execute</a> metotlarını beraber kullanmak gerekir. Query binding yöntemi sql deyimi oluşturulurken sql enjeksiyon tehdidine karşı tehlikeli değerlere kaçış sembolü atar.

Ayrıca eğer uygulamanın bir bölümünde çok fazla aynı sql sorgusu kullanılıyorsa prepare yöntemi sql sorgularını önbelleğe alır ve birbirine eş değer çok fazla sorgu olması durumunda performans sağlar. 

Query binding yöntemini kullandığınızda sql enjeksiyon tehdidine karşı girilen değerlerden <kbd>$this->db->escape()</kbd> metodu ile kaçış yapmanıza gerek kalmaz.

<a name='prepare'></a>

##### $this->db->prepare()

Çalıştırılmak üzere bir sql deyimi hazırlar ve bir deyim nesnesi olarak döndürür.

```php
$this->db->prepare("SELECT * FROM users")->execute()->resultArray();
```

<a name='bindValue'></a>

##### $this->db->bindValue($num, $val, $type)

Bir değeri bir değiştirge ile ilişkilendirir.

```php
$result = $this->db->prepare("SELECT id, username FROM users WHERE id = ? AND active = ?")
->bindValue(1, 2, \PDO::PARAM_INT)
->bindValue(2, 'Active', \PDO::PARAM_STR)
->execute()
->resultArray();
```

Bind value işleminde parametre değerleri tür olarak belirlenir birinci parametre parametrenin numarası, ikinci parametre değeri ve üçüncü parametre ise türüdür. Bu tip sorgularda her değer için kendiliğinden escape yapılır bu nedenle <kbd>$this->db->escape()</kbd> metodunu kullanmaya gerek kalmaz.

<a name='bindParam'></a>

##### $this->db->bindParam($num, $val, $type, $lenght)

Bir değiştirgeyi belirtilen değişkenle ilişkilendirir. Pdo bindValue() yönteminin tersine değişken gönderimli olarak ilişkilendirilir ve sadece execute() çağrısı sırasında değerlendirmeye alınır.

```php
$calories = 150;
$color = 'red';

$result = $this->db->prepare("SELECT name, colour, calories FROM fruit 
WHERE calories < ? AND color = ?")
->bindParam(1, $calories, \PDO::PARAM_INT)
->bindParam(2, $color, \PDO::PARAM_STR, 12)
->execute();
```

Değiştirgeler çoğunlukla girdi değiştirgesidir, yani değiştirgeler sadece sorguda salt okunur olarak ele alınır. Eğer değiştirge çıktı almak amacıyla kullanılacaksa son parametre veri türü uzunluğu mutlaka belirtilmelidir.

<a name='execute'></a>

##### $this->db->execute(array $values)

Bir hazır deyimi girdiler ile çalıştırır.

```php
$result = $this->db->prepare('SELECT name, colour, calories FROM fruit 
WHERE calories < :calories AND colour = :colour')->execute(
    [':calories' => 150, ':colour' => 'red']
);
```

<a name='escape'></a>

##### $this->db->escape()

Eğer <kbd>prepare</kbd> özelliğini kullanmıyorsanız sorgu değerlerini <a href="http://tr.wikipedia.org/wiki/SQL_Injection" target="_blank">sql enjeksiyon</a> güvenlik tehdidine karşı bir kaçış fonksiyonu kullanmanız gerekir. Escape fonksiyonu belirli karakterlerden kaçarak sql cümleciği değerlerini güvenli bir şekilde oluşturmanızı sağlar.

Sql enjeksiyon tehditlerine karşı bağlantıdaki aktif karakter türüne ( charset ) göre girilen karakterlerden kaçar.

```
$title = $this->db->escape("Welcome to John's Blog");   // Welcome to John\'s Blog
$post  = $this->db->escape("This is a dangerous content ' \ ");  // This is a dangerous content \' \\
```

```php
$this->db->exec("INSERT INTO blog (title, post) VALUES ($title, $post)");
```

<a name='transactions'></a>

### Transaksiyonlar

Veritabanı katmanı güvenli transaksiyonu destekleyen tablo türleri ile veri kaybı olmadan veri kaydetmeyi sağlar. MySQL sürücüsü için transaksiyonların çalışabilmesi için MyISAM tablo türü yerine <kbd>InnoDB</kbd> veya <kbd>BDB</kbd> tablo türlerinin kullanılması gerekir. Diğer bilinen veritabanı türleri için transaksiyonlar kendiliğinden desteklenir.

<a name='auto-transaction'></a>

#### Otomatik Transaksiyon

Otomatik transaksiyon bir closure fonksiyonu içerisine konulan veritabanı sorgu operasyonları için transaksiyonları başlatıp commit ve rollBack işlemlerini kendiliğinden yapar. 

```php
$result = $this->db->transactional(
    function () {
        return $this->db->exec("INSERT INTO persons (person_skill, person_name) VALUES ('php', 'Bob')");
    }
);

if ( ! $result) {          
    echo 'Veri kaydetme başarısız. Lütfen tekrar deneyin';
} else {
    echo 'Veri başarı ile kaydedildi. Etkilenen satır sayısı '.$result;
}
```

Eğer transactional() fonksiyonu içerisindeki fonksiyon sonucu <kbd>0</kbd> yada <kbd>false</kbd> ise sonuç her zaman <kbd>true</kbd> değerine dönecektir. Sadece gerçek bir istisnai hata olması durumunda sonuç <kbd>false</kbd> değerine döner. Eğer fonksiyon sonucu 0 dan büyük bir değere dönüyorsa o zaman sonucunun kendisine dönülür.

<a name='native-transaction'></a>

#### Doğal Transaksiyon

Uygulamada transaksiyonları doğal olarak try ... catch komutları ile çalıştırabilmek mümkündür. Bunun için try komutu içerisinde <kbd>beginTransaction</kbd> ile operasyonu başlatıp ve işlemlerin en sonunda başarılı işlemi gönderme anlamına gelen <kbd>commmit</kbd> metodu ile işlemi bitirmeniz gerekir.

```php
try {

    $this->db->beginTransaction(); // Operasyonları başlat
    $this->db->exec("INSERT INTO persons (person_skill, person_name) VALUES ('javascript', 'john')");
    $this->db->commit();      // Operasyonu bitti olarak kaydet

    echo 'Veri başarı ile kaydedildi.';

} catch(Exception $e)
{    
    $this->db->rollBack();    // İşlem başarısız olursa kaydedilen tüm verileri geri al.
    echo $e->getMessage();    // Hata mesajını ekrana yazdır.
}
```

Transaction - Commit metotları arasında birden fazla sorgu çalıştırabilirsiniz ve işlem başarılı ise tüm operasyonlar sisteme <kbd>commit</kbd> edilir, başarısız ise <kbd>rollBack</kbd> komutu ile tüm işlemler başa döndürürülerek <kbd>$e</kbd> Exception nesnesi ile başarısız işlem metotlarına ulaşılır.


<a name='helper-functions'></a>

### Yardımcı Fonksiyonlar

<a name='connect'></a>

##### $this->db->connect()

Bağlanma denemesi yapılarak bağlantının hep canlı kalması sağlanır.

<a name='getDrivers'></a>

##### $this->db->getDrivers()

Mevcut veritabanı sürücü isimlerine geri döner.

<a name='getConnection'></a>

##### $this->db->getConnection()

Varolan veritabanı bağlantı nesnesine geri döner.

<a name='getStmt'></a>

##### $this->db->getStmt()

Varolan PDOStatement nesnesine geri döner.

<a name='inTransaction'></a>

##### $this->db->inTransaction()

Eğer aktif bir transaksiyon işlemi varsa metot <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine geri döner.

<a name='lastInsertId'></a>

##### $this->db->lasInsertId()

Veritabanına en son eklenen tablo id sinin değerine geri döner.

<a name='quoteIdentifier'></a>

##### $this->db->quoteIdentifier(string $name)

Veritabanı sürücüsünde sütun adı yada tablo isimleriyle karışan rezerve edilmiş bir isim var ise bu isme kaçış sembolü atayarak isim çakışmalarının önüne geçer.


<a name='additionalFeatures'></a>

## Ek Özellikler

<a name='queryBuilder'></a>

### Sorgu Oluşturucu ( Query Builder )

Sorgu oluşturucu sql sorgularınızı Object Oriented programlama sitilinde kolayca oluşturmanızı sağlayan bir araçtır. Bu sınıfa ait dökümentasyona [Database-DoctrineQueryBuilder.md](Database-DoctrineQueryBuilder.md) dosyasından ulaşılabilir.