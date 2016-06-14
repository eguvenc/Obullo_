
## Çoklu İş Parçaları

Uygulamanızda php işçilerini eş zamanlı çalıştırarak kuyruktaki verileri tüketme işlemi <kbd>multi threading</kbd> olarak anılır.

<ul>
	<li><a href="#consume">Kuyruğu Eş Zamanlı Tüketmek</a></li>
	<li><a href="#supervisor">Supervisor Kurulumu</a></li>
	<li><a href="#creating-first-worker">İlk İşçimizi Yaratalım</a></li>
	<li><a href="#multiple-workers">Birden Fazla İşçiyi Çalıştırmak</a></li>
	<li><a href="#starting-all-workers">İşçileri Başlatmak</a></li>
	<li><a href="#displaying-all-workers">İşçileri Görüntülemek</a></li>
	<li><a href="#stopping-all-workers">İşçileri Durdurmak</a></li>
	<li><a href="#supervisor-logs">İşçi Logları</a></li>
	<li><a href="#startup-config">Sistem Açılışında Otomatik Başlatma</a></li>
</ul>


<a name="consume"></a>

#### Kuyruğu Eş Zamanlı Tüketmek

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --output=0
```

Yukarıdaki komut aynı anda birden fazla konsolda çalıştırıldığında <kbd>Obullo/Task/QueueController</kbd> sınıfı üzerinden her seferinde  <kbd>Obullo/Task/WorkerController.php</kbd> dosyasını çalıştırarak yeni bir iş parçaçığı oluşturur. Yerel ortamda birden fazla komut penceresi açarak kuyruğun eş zamanlı nasıl tüketildiğini test edebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --output=1
```
Yerel ortamda yada test işlemleri için output parametresini 1 olarak gönderdiğinizde yapılan işlere ait hata çıktılarını konsoldan görebilirsiniz.

Ayrıca UNIX benzeri işletim sistemlerinde prodüksiyon ortamında kuyruk tüketimini otomasyona geçirmek ve çoklu iş parçaları (multithreading) ile çalışmak için Supervisor adlı programdan yararlanabilirsiniz. <a href="http://supervisord.org/" target="_blank">http://supervisord.org/</a>.

<a name="supervisor"></a>

#### Supervisor Kurulumu

Supervisor UNIX benzeri işletim sistemlerinde kullanıcılarının bir dizi işlemi kontrol etmesini sağlayan bir istemci/suncu sistemidir. Daha fazla bilgi için bu adresi <a href="http://supervisord.org/">http://supervisord.org/</a> ziyaret edebilirsiniz.

Kurulum için aşağıdaki komutları konsolunuzdan çalıştırın

```php
sudo apt-get install supervisor
```

Supervisor konsoluna giriş

```php
supervisorctl
```

Tüm yardım komutlarını listelemek

```php
supervisor> help

default commands (type help <topic>):
=====================================
add    clear  fg        open  quit    remove  restart   start   stop  update 
avail  exit   maintail  pid   reload  reread  shutdown  status  tail  version
```

<a name="creating-first-worker"></a>

#### İlk İşçimizi Yaratalım

Supervisor konfigürasyon klasörüne girin.

```php
cd /etc/supervisor/conf.d
```

Tüm konfigürasyon dosyalarını listeleyin.

```php
ll

total 16
drwxr-xr-x 2 root root 4096 May 31 13:19 ./
drwxr-xr-x 3 root root 4096 May 31 13:10 ../
-rw-r--j-- 1 root root  142 May  9  2011 README
```

Favori editörünüzle bir .conf dosyası yaratın.


```php
vi myMailer.conf
```

```php
[program:myMailer]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project/task queue listen --worker:Workers@Mailer --job:mailer.1 --memory:128 --delay=0 --timeout=3
numprocs=3
autostart=true
autorestart=true
stdout_logfile=/var/www/project/data/logs/myMailerProcess.log
stdout_logfile_maxbytes=1MB
```

Burada <kbd>numprocs=3</kbd> sayısı yani 3, işlem başına açılacak iş parçaçığı (thread) anlamına gelir. Bu sayı optimum performans için sunucunuzun işlemci sayısı ile aynı olmalıdır. Örneğin 16 çekirdekli bir makineye sahipseniz bu sayıyı 16 yapabilirsiniz. Böylece bir işi 16 işçi eş zamanlı çalışarak daha kısa zamanda bitirecektir.

Bir işlemci için açılması gereken optimum işçi sayısının neden 1 olması gerektiği hakkındaki makaleye bu bağlantıdan gözatabilirsiniz. <a href="http://stackoverflow.com/questions/1718465/optimal-number-of-threads-per-core">Optimal Number of Threads Per Core</a>.

<a name="multiple-workers"></a>

#### Birden Fazla İşçiyi Çalıştırmak

Birden fazla iş yaratmak için yeni bir konfigürasyon dosyası yaratın ve bu iş için gereken parametreleri girin. Aşağıda resim küçültme işi için bir örnek gösteriliyor.

```php
vi myImages.conf
```

```php
[program:myImages]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project/task queue listen --worker:Workers\ImageResizer --job:images.1 --memory=256 
numprocs=10
autostart=true
autorestart=true
stdout_logfile=/var/www/project/data/logs/myImageResizerProcess.log
stdout_logfile_maxbytes=1MB
```

<a name="starting-all-workers"></a>

#### İşçileri Başlatmak

Tanımladığınız tüm işleri başlatmak için <kbd>start all</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl start all

myMailer_02: started
myMailer_01: started
myMailer_00: started

myImages_02: started
myImages_01: started
myImages_00: started
```

<a name="displaying-all-workers"></a>

#### İşçileri Görüntülemek

Tanımladığınız tüm işleri görmek için <kbd>supervisorctl</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl

myMailer:myMailer_00           RUNNING    pid 16847, uptime 0:01:41
myMailer:myMailer_01           RUNNING    pid 16846, uptime 0:01:41
myMailer:myMailer_02           RUNNING    pid 16845, uptime 0:01:41
```

<a name="stopping-all-workers"></a>

#### İşçileri Durdurmak

Tanımladığınız tüm işleri başlatmak için <kbd>stop all</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl stop all

myMailer_02: stopped
myMailer_01: stopped
myMailer_00: stopped
```
<a name="supervisor-logs"></a>

#### İşçi Logları

İşçi loglarını takip etmek için aşağıdaki komutu konsolunuzdan çalıştırabilirsiniz.

```php
supervisorctl maintail -f
```

<a name="startup-config"></a>

#### Sistem Açılışında Otomatik Başlatma

Bunun için supervisord programını otomatik başlatma dosyanıza ekleyin. Bu dosya kullandığınız işletim sisteminize göre değişkenlik gösterecektir.