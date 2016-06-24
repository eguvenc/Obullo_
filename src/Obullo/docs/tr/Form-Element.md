
## Form Element Sınıfı

Form element sınıfı html formları, html form elementleri ve form etiketi ile ilgili girdileri kolayca oluşturmanıza yardımcı olur. Ayrıca form güvenliğine ilişkin verileri oluşturabilir, örneğin Csrf token form metodu kullanıldığında otomatik olarak oluşturulur.

<ul>
<li><a href="#accessing-methods">Metotlara Erişim</a></li>
<li><a href="#form">$element->form()</a></li>
<li><a href="#formMultipart">$element->formMultipart()</a></li>
<li><a href="#formClose">$element->formClose()</a></li>
<li><a href="#input">$element->input()</a></li>
<li><a href="#password">$element->password()</a></li>
<li><a href="#upload">$element->upload()</a></li>
<li><a href="#textarea">$element->textarea()</a></li>
<li><a href="#dropdown">$element->dropdown()</a></li>
<li><a href="#checkbox">$element->checkbox()</a></li>
<li><a href="#radio">$element->radio()</a></li>
<li><a href="#submit">$element->submit()</a></li>
<li><a href="#reset">$element->reset()</a></li>
<li><a href="#button">$element->button()</a></li>
<li><a href="#dangerous-inputs">Tehlikeli Girdilerden Kaçış</a></li>
</ul>

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$element = $container->get('form')->getElement();
$element->method();
```

Kontrolör içinde,

```php
$element = $this->form->getElement();
$element->method();
```

<a name="form"></a>

#### $element->form($action, $attributes = '', $hidden = array())

Ana konfigürasyon dosyasında tanımlı olan base > url değerine göre form tagı oluşturur. Form taglarını HTML olarak yazmak yerine bu fonksiyon kullanılarak yazılmasının ana faydası web site base url değeri değiştiğinde tüm form url adreslerinizi değiştirmek zorunda kalmamanızdır.

```php
echo $element->form('email/send', " method=get ");
```

```html
<form method="get" action="/email/send" />
```

Nitelikler Eklemek

```php
echo $element->form('email/send', ['class' => 'email', 'id' => 'myform']);
```

```html
<form method="post" action="/email/send"  class="email"  id="myform" />
```

Gizli Girdi Alanları

```php
echo $element->form('email/send', '', ['username' => 'Joe', 'member_id' => '234']);
```

```html
<form method="post" action="/email/send">
<input type="hidden" name="username" value="Joe" />
<input type="hidden" name="member_id" value="234" />
```

<a name="formMultipart"></a>

#### $element->formMultipart($action, $attributes = array(), $hidden = array())

Bu fonksiyon <kbd>$element->form()</kbd> metotu ile aynı işlevleri yerine getirir form metodundan ayrılan yek yönü <kbd>upload</kbd> işlemleri için multipart özelliği eklemesidir.

```php
echo $element->formMultipart('file/upload');
```

```html
<form action="/file/upload" method="post" accept-charset="utf-8" enctype="multipart/form-data">
```

<a name="formClose"></a>

#### $element->formClose($extra = '')

```php
echo $element->formClose("</div></div>");
```

```html
</form>
</div></div>
```

<a name="hidden"></a>

#### $element->hidden($name, $value , $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="hidden"</kbd> olarak ayarlar.

```php
$element->hidden('username', 'johndoe',  $attr = " id='username' " );
```

```html
<input type="hidden" name="username" value="johndoe" id='username'  />
```

Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
              'name'  => 'John Doe',
              'email' => 'john@example.com',
              'url'   => 'http://example.com'
        );
echo $element->hidden($data);
```

```html
<input type="hidden" name="name" value="John Doe" />
<input type="hidden" name="email" value="john@example.com" />
<input type="hidden" name="url" value="http://example.com" />
```

<a name="input"></a>

#### $element->input($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="text"</kbd> olarak ayarlar.

```php
echo $element->input('username', 'johndoe', ' maxlength="100" size="50" style="width:50%"');
```

```html
<input type="text" name="username" id="username" 
value="johndoe" maxlength="100" size="50" style="width:50%" />
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $element->input('username', 'johndoe', ' onclick="someFunction()" ');
```

Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
    'name'      => 'username',
    'id'        => 'username',
    'value'     => 'johndoe',
    'maxlength' => '100',
    'size'      => '50',
    'style'     => 'width:50%',
);
echo $element->input($data);
```

<a name="password"></a>

#### $element->password($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="password"</kbd> olarak ayarlar ve diğer işlevleri <kbd>$element->input()</kbd> metodu ile aynıdır.

<a name="upload"></a>

#### $element->upload($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="file"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$element->input()</kbd> metodu ile aynıdır.

<a name="textarea"></a>

#### $element->textarea($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="textarea"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$element->input()</kbd> metodu ile aynıdır.

```php
$data = array(
    'name'      => 'entry',
    'id'        => 'article',
    'value'     => '',
    'maxlength' => '800',
    'rows'      => '10',
    'cols'      => '5',
);
echo $element->textarea($data);
```

```html
<textarea name="entry" cols="40" rows="10" id="article" maxlength="800" ></textarea>
```

<a name="dropdown"></a>

#### $element-> dropdown($name, $options = '', $selected = '', $attributes = '')

Seçilebilir opsiyonlar girdisi oluşturur. İlk parametre girdi ismini, ikinci parametre seçme opsiyonlarını, üçüncü parametre seçili olan opsiyonları son parametre ise ekstra nitelikleri göndermenizi sağlar.

```php
$options = array(
    'small'  => 'Small Shirt',
    'med'    => 'Medium Shirt',
    'large'  => 'Large Shirt',
    'xlarge' => 'Extra Large Shirt',
);

echo $element->dropdown('shirts', $options, 'large');
```

```html
<select name="shirts">
<option value="small">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>
```

```php
echo $element->dropdown('shirts', $options, ['small', 'large']);
```

```html
<select name="shirts" multiple="multiple">
<option value="small" selected="selected">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $element->dropdown('shirts', $options, 'large', ' id="shirts" onChange="someFunction();" ');
```

<a name="fieldset"></a>

#### $element->fieldset($legent_text = '', $attributes = array())

```php
echo $element->fieldset('Address Information');
echo "<p>fieldset content here</p>\n";
echo $element->fieldsetClose();
```

```html
<fieldset>
<legend>Address Information</legend>
<p>form content here</p>
</fieldset>
```

<a name="fieldsetClose"></a>

#### $element->fieldsetClose($extra = '')

```php
echo $element->fieldsetClose("</div></div>");
```

```html
</fieldset>
</div></div>
```

<a name="checkbox"></a>

#### $element->checkbox($data = '', $value = '', $checked = false, $attributes = '')

```php
echo $element->checkbox('newsletter', 'accept', true);
```

```html
<input type="checkbox" name="newsletter" value="accept" checked="checked" />
```

Üçüncü parametre true/false değeri alır ve kutunun seçili olup olmadığını belirler. Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
    'name'        => 'newsletter',
    'id'          => 'newsletter',
    'value'       => 'accept',
    'checked'     => true,
    'style'       => 'margin:10px',
    );

echo $element->checkbox($data);
```

```html
<input type="checkbox" name="newsletter" id="newsletter" 
value="accept" checked="checked" style="margin:10px" />
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $element->checkbox('newsletter', 'accept', true, ' onClick="someFunction()" ')
```

<a name="radio"></a>

#### $element->radio($data = '', $value = '', $checked = false, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="radio"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$element->checkbox()</kbd> metodu ile aynıdır.

<a name="submit"></a>

#### $element->submit()

```php
echo $element->submit('mysubmit', 'Submit Post!');
```

```html
<input type="submit" name="mysubmit" value="Submit Post" />
```

<a name="reset"></a>

#### $element->reset()

```php
echo $element->reset('myreset', 'Reset Form');
```

```html
<input type="reset" name="myreset" value="Reset Form"  />
```

<a name="button"></a>

#### $element->button()


```php
echo $element->button('name', 'Content');
```

```html
<button name="name" type="button">Content</button> 
```

Array türünden veri gönderilerek de yaratılabilirler.


```php
$data = array(
    'name'    => 'button',
    'id'      => 'button',
    'value'   => 'true',
    'type'    => 'reset',
    'content' => 'Reset'
);

echo $element->button($data);
```

```html
<button name="button" id="button" value="true" type="reset">Reset</button>  
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $element->button('mybutton', 'Click Me', ' onClick="someFunction()" ');
```

<a name="dangerous-inputs"></a>

### Tehlikeli Girdilerden Kaçış

Form içerisinde <kbd>"</kbd> veya <kbd>'</kbd> gibi form yapısını bozan karakterler ve html karakterlerini güvenli bir şekilde kullanmanıza olanak tanır.

```php
$string = 'Tehlikeli girdiler içeren <strong>"alıntılı"</strong> yazı.';

<input type="text" name="myform" value="<?php echo $string ?>" />
```

Yukarıdaki veri çift tırnak karakterleri içerir ve form girdi yapısını bozar. <kbd>$this->clean->escape()</kbd> metodu ise aşağıdaki gibi html karakterlerini kodlayarak form içinde güvenli bir şekilde kullanılmasını sağlar.

```php
<input type="text" name="myform" value="<?php echo $this->clean->escape($string); ?>" />
```

Eğer form element sınıfı fonksiyonlarını zaten kullanıyorsanız bu fonksiyonu kullanmaya ihtiyacınız kalmaz çünkü form değerleri otomatik olarak güvenli formata dönüştürülür. Bu fonksiyonu yalnızca kendi form elementlerinizi oluşturduğunuz zaman kullanmanız gerekir.

Yinede detaylı <kbd>Girdi Filtreleme</kbd> için [Filters.md](Filters.md) dökümentasyonunu gözden geçirmeyi unutmayın.