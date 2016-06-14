
## Oturum Açma Olayı

Oturum açma aşamasında yetki doğrulama paketi içerisinde <kbd>LoginEvent</kbd> olayı tetiklenir. Bu olayı başlatan sınıf <kbd>app/classes/Event/LoginEvent.php</kbd> dosyasıdır.

#### Olay

```php
namespace Event;

use League\Event\Emitter;
use League\Event\AbstractEvent;
use Obullo\Container\ContainerAwareInterface;
use Interop\Container\ContainerInterface as Container;

class LoginEvent extends AbstractEvent
{
    public function __construct(Container $container)
    {
        $emitter = new Emitter;

        if ($emitter instanceof ContainerAwareInterface) {
            $emitter->setContainer($container);
        }
        $this->setEmitter($emitter);
    }
}
```

### Oturum Açma Olayını Dinlemek

Oturum açma olayı <kbd>app/classes/Event/LoginResultListener.php</kbd> dosyası tarafından dinlenir. Şimdi dinleyici sınıfına bir göz atalım.

#### Dinleyici

```php
namespace Event;

use League\Event\AbstractListener;
use League\Event\EventInterface as Event;

use Obullo\Authentication\AuthResult;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

class LoginResultListener extends AbstractListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function handle(Event $event, AuthResult $authResult = null)
    {
        $container = $this->getContainer();

        if ($authResult->isValid()) {

            $row = $authResult->getResultRow();  // Get query results

        } else {

            // Store attemtps
            // ...
        
            $identifier = $authResult->getIdentifier();

        }
    }

}
```

Yukarıda görüldüğü  gibi <kbd>LoginResultListener</kbd> sınıfı ile oturum açma denemesinin başarılı olup olmamasına göre oturum açma verilerini dinleyebilir ve uygulamanızı özelleştirebilirsiniz.