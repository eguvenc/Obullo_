
## Model nedir ?

Modeller veritabanı ile haberleşmeyi sağlayan ve veritabanı fonksiyonları için tasarlanmış php sınıflarıdır. Örnek verecek olursak bir blog uygulaması yaptığımızı düşünelim bu uygulamada yer alan model sınıflarınıza <kbd>insert, update, delete</kbd> ve  <kbd>get</kbd> metotları koymanız beklenir.Model sınıfı size uygulamada ayrı bir katman sağlar ve veritabanı kodlarınızı bu katmanda geliştirmeniz kodlarınızın sürekliliğine, esnekliğine ve test edilebilirliğine yardımcı olur.

Uygulamanızda model katmanı kullandığınızda <kbd>sorgu önbellekme</kbd>, <kbd>testler</kbd>, <kbd>veritabanı kodlarının bakımı</kbd> gibi problemler kolaylıkla çözülür.

### Modelleri Yüklemek

```php
$this->model = new \Model\Foo\Bar;
$this->model->method();
```

### Model Klasörü Yaratmak

Model sınıfları <kbd>app/classes/Model</kbd> klasöründen çağırılır. Aşağıdaki örnek, modellerin nasıl kullanılabileceği hakkında size bir fikir verebilir.

```php
+ app
 - classes
    - Model
		  Entry.php
```

Önce <kbd>classes</kbd> klasörü altında Model adında bir klasörünüz yoksa bu isimde bir klasör yaratın ve içerisine aşağıdaki gibi <kbd>Entry.php</kbd> adında bir dosya oluşturun. Model sınıflarını yaratırken aynı sınıf yapılarında olduğu gibi dosya adı ve klasör adları büyük harfle yazılmalıdır.

Aşağıda sizin için bir örnek yaptık ve bu örneğe ait sql kodu aşağıdaki gibi.

```php
--
-- Table structure for table `entries`
--

CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

```

#### Entry.php

```php
namespace Model;

use Obullo\Database\Model;

class Entry extends Model
{
    public $title;
    public $content;
    public $date;

    public function findOne($id = 1)
    {
        return $this->db->prepare("SELECT * FROM entries WHERE id = ?")
            ->bindParam(1, $id, \PDO::PARAM_INT)
            ->execute()
            ->rowArray();
    }

    public function findAll($limit = 10)
    {
        return $this->db->prepare("SELECT * FROM users LIMIT ?")
            ->bindParam(1, $limit, \PDO::PARAM_INT)
            ->execute()
            ->resultArray();
    }

    public function insert()
    {
        return $this->db->insert(
            'entries', 
            [
                'title' => $this->title, 
                'content' => $this->content,
                'date' => $this->date
            ], 
            [
                'title' => \PDO::PARAM_STR,
                'content' => \PDO::PARAM_STR,
                'date' => \PDO::PARAM_INT,
            ]
        );
    }

    public function update($id)
    {
        return $this->db->transactional(
            function () 
            {
                return $this->db->update(
                    'entries', 
                    [
                        'title' => $this->title, 
                        'content' => $this->content,
                        'date' => $this->date
                    ], 
                    ['id' => 1], 
                    [
                        'id' => \PDO::PARAM_INT,
                        'title' => \PDO::PARAM_STR,
                        'content' => \PDO::PARAM_STR,
                        'date' => \PDO::PARAM_INT,
                    ]
                );
            }
        );
    }

    public function delete($id)
    {
        return $this->db->transactional(
            function () use ($id)
            {
                return $this->db->delete('entries', ['id' => $id], ['id' => \PDO::PARAM_INT]);
            }
        );
    }

}

/* Location: .app/classes/Model/Entry.php */
```

Yukarıdaki örnekteki uygulamaya özgü <kbd>findAll()</kbd> ve <kbd>findOne()</kbd> metotları veritabanından <kbd>okuma</kbd> işlemleri yaparken diğer metotlar veritabanına <kbd>yazma</kbd> işlemi yaparlar. Eğer veritabanına veri kaybı olmadan yazma işlemleri yapmak istiyorsak yukarıdaki örneklerde olduğu gibi fonksiyonlarınızı <kbd>transactional()</kbd> metodu içerisinde kullanmamız gerekir.

```php
$result = $this->db->transactional(
    function () use ($data) {

        return $this->db->insert(
            'entries', 
            $data
        );
    }
);

if ( ! $result) {          
    echo 'Veri kaydetme başarısız. Lütfen tekrar deneyin';
} else {
    echo 'Veri başarı ile kaydedildi. Etkilenen satır sayısı '.$result;
}
```

Transaction metodu, içerisine konulan isimsiz fonksiyonları çalıştırır ve çalışma aşamasında <kbd>commit</kbd> işlemi başarılı ise işlemler veritabanına işlenir. İşlem başarılı olduğunda metot içerisindeki isimsiz fonksiyonun sonucuna geri dönülür aksi durumda uygulama bir <kbd>PDOException</kbd> hatası fırlatır ve hata mesajı görüntülenirken yapılan yazma işlemleri içeriden <kbd>rollBack</kbd> metodu ile geri alınır. Eğer veritabanına kaydettiğiniz veriler kritik düzeyde önemli veriler ise yazma işlemlerinde veri kaybı olmaması için transaction metodu kullanmanız tavsiye edilir. PDOException ve diğer RuntimeException hataları <kbd>app/errors.php</kbd> dosyasından kontrol edilirler.

Aşağıda entry modelinin kontrolör sınıfı içerisinde nasıl kullanılacağına dair bir örnek gösteriliyor.


```php
namespace Welcome;

use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function load()
    {
        $this->entry = new \Model\Entry;
    }

    public function index()
    {
    	$rowArray = $this->entry->findOne(1);

		print_r($rowArray);
    }

    public function insert()
    {
        $this->entry->title = 'Insert Test';
        $this->entry->content = 'Hello World';
        $this->entry->date = time();
        $this->entry->insert();

        echo 'New entry added.';
    }

    public function update($id)
    {
        $this->entry->title = 'Update Test';
        $this->entry->content = 'Welcome to my world';
        $this->entry->date = time();
        $this->entry->update($id);

        echo 'Entry updated.';    
    }

    public function delete($id)
    {
        $this->entry->delete($id);
    }
}

/* Location: .modules/welcome/welcome.php */
```