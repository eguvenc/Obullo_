
## Veritabanı Sorgularını Özelleştirmek

Yetki doğrulama paketi kullanıcıya ait database fonksiyonlarını servis içerisinden <kbd>Obullo\Authentication\Model\Database</kbd> sınıfından çağırır. Eğer mevcut database sorgularında değişlik yapmak istiyorsanız bu sınıfa genişlemek için önce auth konfigürasyon dosyasından <kbd>db.model</kbd> anahtarını <kbd>\Auth\Model\Database</kbd> olarak değiştirin.

```php
return array(
    
    'params' => [

        'db.adapter' => 'Obullo\Authentication\Adapter\Database',
        'db.model' => 'Auth\Model\Database',
        'db.provider' => [
            'connection' => 'default'
        ],
        .
)
```

Sonraki adımda <kbd>Database.php</kbd> dosyasını yaratarak aşağıdaki gibi <kbd>Obullo\Authentication\Model\Database</kbd> sınıfına genişlemeniz önerilir. Bu sınıf <kbd>\Obullo\Authentication\Model\ModelInterface</kbd> sınıfını metotlarını baz alır.

```php
namespace Obullo\Authentication\Model;

interface ModelInterface
{
    public function __construct(Container $container, array $params);
    public function query(array $credentials);
    public function recallerQuery($token);
    public function updateRememberToken($token, array $credentials);
}
```


Aşağıda sizin için bir model örneği yaptık bu örneği değiştererek ihtiyaçlarınıza göre kullanabilirsiniz. Bunun için <kbd>Obullo\Authentication\Model\Database</kbd> sınıfına bakın ve ezmek ( override ) istediğiniz method yada değişkenleri sınıfınız içerisine dahil edin.

```php
namespace Auth\Model;

use Obullo\Authentication\Model\ModelInterface;
use Obullo\Authentication\Model\Database as AuthModel;

class Database extends AuthModel
{
    public function query(array $credentials)
    {
        return $this->db->prepare(sprintf(
            'SELECT * FROM %s WHERE %s = ?', $this->tablename, $this->columnIdentifier
        ))
            ->bindValue(1, $credentials[$this->columnIdentifier], PDO::PARAM_STR)
            ->execute()
            ->rowArray();
    }

}

/* Location: .app/classes/Auth/Model/Database.php */
```