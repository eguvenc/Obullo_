
## Form Doğrulama Metotları

Doğrulama işlemi içerisinde form sınıfı <kbd>get</kbd> metotları, verileri view dosyalarına bağlamak yada <kbd>set</kbd> metotları ile form nesnesine veri göndermek için kullanılır.

<ul>
    <li><a href="#formSetMessage">$this->form->setMessage()</a></li>
    <li><a href="#formSetErrors">$this->form->setErrors()</a></li>
    <li><a href="#formGetError">$this->form->getError()</a></li>
    <li><a href="#formIsError">$this->form->isError()</a></li>
    <li><a href="#formGetErrorClass">$this->form->getErrorClass()</a></li>
    <li><a href="#formGetErrorLabel">$this->form->getErrorLabel()</a></li>
    <li><a href="#formGetValue">$this->form->getValue()</a></li>
    <li><a href="#formSetValue">$this->form->setValue()</a></li>
    <li><a href="#formsetSelect">$this->form->setSelect()</a></li>
    <li><a href="#formSetCheckbox">$this->form->setCheckbox()</a></li>
    <li><a href="#formSetRadio">$this->form->setRadio()</a></li>
</ul>

<a name="formSetMessage"></a>

#### $this->form->setMessage($message)

Form çıktı dizisi içerisinde oluşturulan <kbd>messages</kbd> anahtarına bir form mesajı ekler. Detaylı bilgi için [Form.md](Form.md) dökümentasyonunu inceleyebilirsiniz.

<a name="formSetErrors"></a>

#### $this->form->setErrors(object $validator | array $errors)

Form sınıfına doğrulama nesnesinden dönen hata ve değerleri göndermek için bu metot kullanılır.

```php
if ($this->request->isPost()) {

    if ($this->validator->isValid()) {          
        $this->form->success('Success');
    } else {
        $this->form->error('Fail');
    }
    $this->form->setErrors($this->validator);
}
```

Form post işleminde sonra <kbd>validator</kbd> nesnesi form sınıfına referans olarak gönderilir. Böylece view kısmında form nesnesi üzerinden validator değerlerine ulaşılmış olur.

Form mesajları

```php
echo $this->form->getMessageString()
```

Form hataları

```php
 <form name="example" action="/examples/forms/form" method="POST">
    <?php echo $this->form->getError('email') ?>
    <input type="email" name="email" value="<?php echo $this->form->getValue('email') ?>">

    <?php echo $this->form->getError('password') ?>
    <input type="password" name="password" id="pwd" placeholder="Password">
  <button type="submit" class="btn btn-default">Submit</button>
</form>
```

<a name="formGetErrors"></a>

#### $this->form->getError($field)

Doğrulamadan sonra form alanı hatalı ise ilgili alana ait hata mesajını ekrana yazdırır.

```php
echo $this->form->getError('email', $prefix = '<p>', $suffix = '</p>')
```

Çıktı

```php
The Email field is required.
```

Doğrulama sınıfı array türündeki alanları da destekler.

```php
<input type="text" name="options[]" value="" size="50" />
```

Bu türden bir element isminin doğrulaması için form kuralına da aynı isimle girilmesi gerekir.

```php
$this->validator->setRules('options[]', 'Options', 'required');
```

Eğer checkbox element türünde birden fazla alan isteniyorsa,

```php
<input type="checkbox" name="options[]" value="red" />
<input type="checkbox" name="options[]" value="blue" />
<input type="checkbox" name="options[]" value="green" /> 
```

Bu türden bir elemente ait hata mesajını almak için metot içinde aynı isim kullanılır.

```php
echo $this->form->getError('options[]');
```

hatta alan ismi çok boyutlu bir array içerse bile,

```php
<input type="checkbox" name="options[color][]" value="red" />
<input type="checkbox" name="options[color][]" value="blue" />
<input type="checkbox" name="options[color][]" value="green" /> 
```

yine isim aşağıdaki gibi girilir.

```php
echo $this->form->getError('options[]');
```

<a name="formIsError"></a>

#### $this->form->isError($field)

Eğer girilen alana ait bir hata varsa true aksi durumda false değerine döner.

<a name="formGetErrorClass"></a>

#### $this->form->getErrorClass($field)

Eğer girilen alana ait hata dönerse <kbd>providers/form.php</kbd> dosyasından

```php
'error' => [
    'class' => 'has-error has-feedback',
]
```
<kbd>error > class</kbd> konfigürasyonu

```php
echo $this->form->getErrorClass('email')
```

aşağıdaki gibi çıktılanır.


```php
has-error has-feedback    
```

<a name="formGetErrorLabel"></a>

#### $this->form->getErrorLabel($field)

Eğer girilen alana ait hata dönerse <kbd>providers/form.php</kbd> dosyasından

```php
'error' => [
    'label' => '<label class="control-label" for="%s">%s</label>
]
```

<kbd>error > label</kbd> konfigürasyonu

```php
echo $this->form->getErrorLabel('email')
```

aşağıdaki gibi çıktılanır.

```php
<label class="control-label" for="field">Label</label>
```

<a name="formGetValidationErrors"></a>

#### $this->form->getValidationErrors();

<kbd>$validator->getErrorString()</kbd> metoduna geri döner.

<a name="formGetValue"></a>

#### $this->form->getValue()

Doğrulanmış bir form elementinin son değerine geri döner.

<a name="formSetValue"></a>

#### $this->form->setValue($field, $value = '')

Input yada textarea türündeki bir form elementine değer girmeyi sağlar. İlk parametreye input ismi girilmek zorundadır. İkinci parametre opsiyoneldir ve input alanı için varsayılan değeri tanımlar.

```php
<input type="text" name="quantity" 
value="<?php echo $this->form->setValue('quantity', '0'); ?>" size="50" />
```

Yukarıdaki örnekte form elementi sayfa ilk yüklendiğinde 0 değerini gösterir.

<a name="formSetSelect"></a>

#### $this->form->setSelect()

Eğer bir <kbd>select</kbd> menü kullanıyorsanız, bu fonksiyon menüye ait seçilen opsiyonları göstermeyi sağlar. İlk parametre select menü ismini belirler, ikinci parametre ise her bir opsiyon değerini içermek zorundadır. Üçüncü parametre ise opsiyoneldir, opsiyon değerinin varsayılan olarak gösterilip gösterilmeyeceğini belirler ve boolean tipinde olmalıdır.

```php
<select name="myselect">
<option value="one" <?php echo $this->form->setSelect('myselect', 'one', true); ?> >One</option>
<option value="two" <?php echo $this->form->setSelect('myselect', 'two'); ?> >Two</option>
<option value="three" <?php echo $this->form->setSelect('myselect', 'three'); ?> >Three</option>
</select>
``` 

<a name="formSetCheckbox"></a>

#### $this->form->setCheckbox()

Array türünde bir element isminin doğrulanması için form kuralına da aynı ismin girilmesi gerekir.

```php
$this->validator->setRules('options[]', 'Options', 'required');
```
Form post işleminden sonra seçilen checkbox element değerini seçili hale getirmek için aşağıdaki yöntem kullanılır.

```php
echo $this->form->setCheckbox('options[]', 'red');
```

```php
<label>
<input type="checkbox" name="options[color][]" 
value="red" <?php echo $this->form->setCheckbox('options[]', 'red') ?> />
Red
</label>

<label>
<input type="checkbox" name="options[color][]" 
value="blue" <?php echo $this->form->setCheckbox('options[]', 'blue') ?> />
Blue
</label>

<label>
<input type="checkbox" name="options[color][]" 
value="green" <?php echo $this->form->setCheckbox('options[]', 'green') ?>  />
Green
</label>
```

<a name="formSetRadio"></a>

#### $this->form->setRadio()

Form post işleminden sonra seçilen radio element değerini seçili hale getirmek için aşağıdaki yöntem kullanılır, $this->form->setCheckbox() metodu ile aynı işlevselliğe sahiptir.

```php
<input type="radio" name="myradio" value="1" <?php echo $this->form->setRadio('myradio', '1', true) ?> />
<input type="radio" name="myradio" value="2" <?php echo $this->form->setRadio('myradio', '2') ?> />
```

Form sınıfı ile ilgili daha ayrıntılı bilgi için [Form.md](Form.md) dökümentasyonuna göz atın.

