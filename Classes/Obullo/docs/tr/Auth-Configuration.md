
## Yetki Doğrulama Konfigürasyonu

Yetki doğrulama paketine ait konfigürasyon <kbd>providers/user.php</kbd> dosyasından yönetilir.

### Değerler tablosu

<table>
    <thead>
        <tr>
            <th>Anahtar</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache[key]</td>
            <td>Bu değer auth paketinin kayıt olacağı anahtarın önekidir. Bu değeri her proje için farklı girmeniz tavsiye edilir. Örneğin bu değer <kbd>Auth:ProjectName</kbd> olarak girilebilir.</td>
        </tr>
        <tr>
            <td>cache[storage]</td>
            <td>Hazıfa deposu yetki doğrulama esnasında kullanıcı kimliğini ön belleğe alır ve tekrar tekrar oturum açıldığında database ile bağlantı kurmayarak uygulamanın performans kaybetmesini önler.</td>
        </tr>
        <tr>
            <td>cache[provider][connection]</td>
            <td>Hazıfa deposu içerisinde kullanılan servis sağlayıcısının hangi bağlantıyı kullanacağını belirler.</td>
        </tr>
        <tr>
            <td>cache[block][permanent][lifetime]</td>
            <td>Oturum açıldıktan sonra kullanıcı kimliği verileri <kbd>permanent</kbd> yani kalıcı hafıza bloğuna kaydedilir. Kalıcı blokta ön belleğe alınan veriler kullanıcının web sitesi üzerinde <kbd>hareketsiz</kbd> kaldığı andan itibaren varsayılan olarak <kbd>3600</kbd> saniye sonra yok olur.</td>
        </tr>
        <tr>
            <td>cache[block][temporary][lifetime]</td>
            <td>Opsiyonel olarak gümrükten pasaport ile geçiş gibi kimlik onaylama sistemi isteniyorsa,  oturum açıldıktan sonra kullanıcı kimliği verileri <kbd>$this->user->identity->makeTemporary()</kbd> komutu ile <kbd>temporary</kbd> hafıza bloğuna taşınabilir. Geçici bloğa taşınmış veriler <kbd>300</kbd> saniye sonrasında yok olur. Kimlik onayladı ise <kbd>$this->user->identity->makePermanent()</kbd> komutu ile kimlik kalıcı hale getirilir ve kullanıcı sisteme giriş yapmış olur.
            </td>
        </tr>
        <tr>
            <td>security[passwordNeedsRehash][cost]</td>
            <td>Bu değer <kbd>password</kbd> kütüphanesi tarafından şifre hash işlemi için kullanılır. Varsayılan değer 6 dır fakat maximum 8 ila 12 arasında olmalıdır aksi takdirde uygulamanız yetki doğrulama aşamasında performans sorunları yaşayabilir. 8 veya 10 değerleri orta donanımlı bilgisayarlar için 12 ise güçlü donanımlı ( çekirdek sayısı fazla ) bilgisayarlar için tavsiye edilir.</td>
        </tr>
        <tr>
            <td>session[regenerateSessionId]</td>
            <td>Session id nin önceden çalınabilme ihtimaline karşı uygulanan bir güvenlik yöntemidir. Bu opsiyon aktif durumdaysa oturum açma işleminden önce session id yeniden yaratılır ve tarayıcıda kalan eski oturum id si artık işe yaramaz hale gelir.</td>
        </tr>
        <tr>
            <td>login[rememberMe]</td>
            <td>Eğer kullanıcı beni hatırla özelliğini kullanarak giriş bilgilerini kalıcı olarak tarayıcısına kaydetmek istiyorsa  <kbd>__rm</kbd> isimli bir çerez ilk oturum açmadan sonra tarayıcısına kaydedilir. Bu çerezin sona erme süresi varsayılan olarak 6 aydır. Kullanıcı farklı zamanlarda uygulamanızı ziyaret ettiğinde eğer bu çerez ( remember token ) tarayıcısında kayıtlı ise Identity sınıfı içerisinde <kbd>Authentication\Recaller->recallUser($token)</kbd> metodu çalışmaya başlar ve beni hatırla çerezi veritabanında kayıtlı olan değer ile karşılaştırılır, değerler birbiri ile aynı ise kullanıcı sisteme giriş yapmış olur. Güvenlik amacıyla her oturum açma (login) ve kapatma (logout) işlemlerinden sonra bu değer çereze ve veritabanına yeniden kaydedilir.</td>
        </tr>
    </tbody>
</table>