
## Olay Sınıfı

Olay sınıfı uygulama içerisinde olaylar ilan edip ve bu olayları bağımsız olarak belirlediğiniz dinleyici sınıflar içerisinden yönetmenizi sağlar. Çerçeve içerisinde olay paketi harici olarak kullanılır ve <a href="http://thephpleague.com" target="blank">The Php League</a> php grubu tarafından geliştirilen <a href="http://event.thephpleague.com/" target="_blank">Event</a> paketi tercih edilmiştir.

<ul>
    <li><a href="#emitters">Emitörler</a></li>
    <li><a href="#events">Olaylar</a></li>
    <li><a href="#listeners">Dinleyiciler</a></li>
</ul>

<a name="emitters"></a>

#### Emitörler

Emitörler olayları başlatmayı ve dinleyicileri kontrol etmeyi sağlarlar.

```php
use League\Event\Emitter;

$emitter = new Emitter;
$emitter->addListener('domain.event', new DomainListener);
$emitter->emit('domain.event');
```

<a name="events"></a>

#### Olaylar

<kbd>AbstractEvent</kbd> sınıfına genişleyerek <kbd>Emitter</kbd> nesnesini event içerisinden de kontrol edebiliriz.

```php
use League\Event\Emitter;
use League\Event\AbstractEvent;

class DomainEvent extends AbstractEvent
{
    public function __construct()
    {
        $emitter = new Emitter;
        $this->setEmitter($emitter);
    }
}
```

```php
$event = new DomainEvent;
```

Böylece tek bir olay nesnesi ile tüm nesnelere hakim olmak mümkün olur.

<a name="listeners"></a>

#### Dinleyiciler

Dinleyicilerin sınıf tabanlı olması bize daha temiz ve güzel bir dinleme yönetimi sağlar. Emitörler sadece <kbd>ListenerInterface</kbd> arayüzünü uygulayan sınıfları kabul ederler. Bu arayüz normalde <kbd>isListener</kbd> ve <kbd>handle</kbd> metotlarının dinleyici sınıfı içerisinde tanımlı olmaya zorlar. Fakat aşağıdaki gibi <kbd>AbstractListener</kbd> sınıfını kullanıyorsak sadece <kbd>handle</kbd> metodunu uyarlamak yeterli olur.

```php
use League\Event\AbstractListener;
use League\Event\EventInterface;

class DomainListener extends AbstractListener
{
    public function handle(EventInterface $event, $param = null)
    {
        // Handle the event.

        echo $event->getName();  // domain.event

        var_dump($param);  // Hello Events !
    }
}
```

Bir olay yaratak emitör nesnesine ulaşıyoruz.

```php
$event = new DomainEvent;
$emitter = $event->getEmitter();
```

Dinleyiciyi ekleyelim.

```php
$emitter->addListener('domain.event', new DomainListener);
```

ve son olarak varsa parametreleri ile birlikte olayı fırlatalım.

```php
$emitter->emit('domain.event', 'Hello Events !');
```

Çıktı

```php
domain.event
Hello Events !
```

Daha ayrıntılı bilgi için <a href="http://event.thephpleague.com">http://event.thephpleague.com</a> adresini ziyaret edebilirsiniz.