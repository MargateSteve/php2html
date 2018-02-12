Php2Html
===================

Php2Html is a set of Php functions to create HTML tags and other elements within a script, rather than switching between Php and HTML.

The tags are fully nestable and can contain all standard attributes such as id's, classes, styles and HTML5 data attributes. Most attributes that can be used by individual tags are also available but there is a fall back to enforce any that are not yet included.

The main focus is on the tags but the plan is to include other HTML elements that bundle these together. Currently, only a table builder element is present.

*Note that this an experimental class and as such, should be used with some caution. It will not cause your web server to explode **BUT MAY NOT** work 100% as expected at this early stage.*   

## Table of contents
- [Install and set-up](#installation)
- [Usage](#usage)
    - [Tags](#tags)
    - [Table Builder](#table-builder)
- [Available attributes](#available-attributes)
- [List of tags](#list-of-tags-and-their-function-names)
- [Examples](#examples)
- [About](#about)
    - [Concept](#concept)
    - [Version History](#version-history)

## Installation
Php2Html has no dependencies other than it can only be used on a server running PHP5+ (either on a web host or locally with WAMP / XAMPP).

No specific set-up is required. Simply save Php2Html.php within your project and call it in the file that you want to include it with include('path/to/Php2Html.php').

The example files, include Php2Html from the direct parent folder (to match their location). If you move the example files to a folder that is not in the same directory as the class file, you will need to change the link.


## Usage
All functions are static so need to be called as a static method
```php
Php2Html::function($params)
```
Any required parameters, such as content to display, id's, classes etc are pass in to the function as an array.

For the tags functions, these are simply the attributes and content for the entire tag, while for the table function, these control cell, row, column and table individual styles and the array of table data.

## Tags
Most of the commonly used HTML tags are available in Php2Html and you simply call the function with the same name as the tag and provide the details in an array.

At their simplest level, these will only contain the content to display but can also contain most HTML5 attributes.

By default, the majority of tags also automatically generate a HTML comment to append to the end of the tag (td and tr do this as it is not really required). You can turn this off on a by-case basis by including *'show_comment' => false* as part of the array when you call it.

The method to show the content varies depending on the type of tag. Tags that both open and close, require a 'content' element in the array.

```php
echo Php2Html::div ([
    'content' => 'Div Content'
]);
```
returns
```html
<div>Div Content</div><!-- div -->
```


Single tags require a 'value' element in the array (as it is filling the 'value' attribute).

```php
echo Php2Html::input_text ([
    'value' => 'Default Input'
]);
```
returns
```html
<input type="text" value="Default Input"><!-- input -->
```

Note that in the examples, we use the short array syntax. You can also use the classic style if that is your preferred way.

```php
echo Php2Html::div ([
    array (
        'content' => 'Div Content'
    )
);
```

Although it is unlikely that you would want to use this for single tags, the fact that you can nest and concatenate content means you can build whole sections in one go.

Combining the two examples above we could use
```php
echo Php2Html::div ([
    'content' => Php2Html::input_text ([
        'value' => 'Default Input'
    ])
]);
```
or
```php
echo Php2Html::div (
    array (
        'content' => Php2Html::input_text (
            array(
                'value' => 'Default Input'
            )
        )
    )
);
```
to give us
```html
<div><input type="text" value="Default Input"><!-- input --></div><!-- div -->
```
*Note that all output is minified. A future plan is to find a way to prettify the output.*

For more complex sections, you may want to build it in parts, and then put it all together at the end.

Taking the below example
```html
<div>
    <p>Paragraph one<p><!-- p -->
    <p>Paragraph two<p><!-- p -->
    <p>Paragraph three<p><!-- p -->
</div><!-- div -->
```

you could build each paragraph individually and concatenate them in the div at the end.

``` php
$p1 = Php2Html::p ([
    'content' => 'Paragraph one'
]);

$p2 = Php2Html::p ([
    'content' => 'Paragraph two'
]);

$p3 = Php2Html::p ([
    'content' => 'Paragraph three'
]);

echo Php2Html::div ([
    'content' => $p1.$p2.$p3
]);
```

To add any attributes to a tag, simply add them to the array when calling. In this early version, these are not necessarily only usable within tags that they apply to so, although nothing will break, using the wrong thing in the wrong place will not comply with HTML standards.
Most tags are simply a key => value pairing but html data or aria attributes get passed in as an array.

```php
echo Php2Html::div ([
    'content' => 'Div Content',
    'id' => 'main-div',
    'class' => 'bg-black text-white',
    'style' => 'height:50px;width:100px;',
    'data_attr' =>[
        'ref' => '45',
        'category' => 'news'
    ]
]);
```

```html
<div id="main-div" class="bg-black text-white" data-ref="45" data-category="news" style="height:50px;width:100px;">
    Div Content
</div><!-- div #main-div .bg-black text-white -->
```

If you want to add specific info to the HTML comment, you can pass that in the array as 'comment'.

```php
echo Php2Html::div ([
    'content' => 'Div Content',
    'id' => 'main-div',
    'class' => 'bg-black text-white',
    'comment' => 'My Example Div'
]);
```
returns

```html
<div id="main-div" class="bg-black text-white">
    Div Content
</div><!-- div #main-div .bg-black text-white My Example Div -->
```

There is also a catchall option to allow any attributes that are currently not available in the class or create custom ones. By adding 'controls' => 'xxx' to the array, you can send a string of anything to be placed in the tag.

```php
echo Php2Html::div ([
    'content' => 'Div Content',
    'id' => 'main-div',
    'controls' => 'role="alert" tabindex="4"'
]);
```
returns

```html
<div id="main-div" role="alert" tabindex="4">
    Div Content
</div><!-- div #main-div -->
```

A list of the [attributes](#available-attributes) can be found at the bottom of this file along with all available [tags](#list-of-tags-and-their-function-names).

## Table Builder
The make_table() function takes an array and relevant parameters and creates a full HTML table.

There are three different parameters that can be passed
in, but each of these are an array containing other
parameters.

- **'data'** - contains the records to be placed in the table rows.

- **'columns'** - contains a separate array for each column to be
shown, as well as any classes to be applied to either the th,
td or both.
The key for each column will the database column name. The other
parameters that can be used are
  - **'alias'** - if the table has a header, this will be the name
		shown in the th.
  -	**'td_class**' - a class to be added to the columns td.
  - **'th_class'** - a class to be added to the columns th.
  - **'global_class'** - a class to be added to both the th and td.

- **'settings'** - contains anything relating to the table itself,
such as whether to show a header and any table styles. The
parameters that can be used are
  - **'show_header'** - true/false to set whether to show the thead.
  - **'border'** - adds a border to the table with a specified width.
  - **'class'** - adds the specified class(es) to the whole table.

You can also add a class to a table row by adding a 'tr_class'
element to the relevant record in the array.

There is an [example](#examples) file that shows a slightly, complex table builder set up, where data is converted to links and various styles are applied.

## Available attributes
Php2Html supports the majority of available attributes by default. You can add any attribute to any tag without causing any issue but certain ones will only actually work with specific tags (e.g if you add 'href' to a div, it will show but will not turn the div into a link).

It would be safe to assume that global attributes of id, class, title, ref and data-attr (HTML5 data attributes) can be used against all tags.
The 'controls' option as mentioned in the [tags usage](#tags) section can also be used globally as can setting an inline 'style'.

The following attributes can only be used against the specified tags. Most are straightforward HTML attribute names but any that work slightly differently will be explained here and in the [list of tags](#list-of-tags-and-their-function-names), as well as commented within the functions themselves.

#### action
- form

#### alt
- img

#### button_type
- button

#### checked
- checkbox, radio

#### cite
- blockquote

#### cols
- textarea,

#### colspan
- td, th

#### data_attr
- all

These are the HTML5 data attributes and need to be supplied as a key=>value pairing.
'data-attr'=>['title'=>'My Element', 'ref'=>'99'] will return data-title="My Element" and data-ref="99"

#### datetime
- time

#### disabled
- button, checkbox, fieldset, radio

#### for
- label

#### form
- all inputs, button, fieldset

#### height
- all inputs, img, embed

#### href
- a

#### label
- optgroup, option

#### method
- form

#### max
- input_range, input_date

#### maxlength
- all inputs

#### min
- input_range, input_date

#### minlength
- all inputs

#### multiple
- input_email, input_file, select

#### name
- all inputs, button, checkbox

#### placeholder
- all inputs

#### required
- all inputs,

#### rel
- a

#### rows
- textarea

#### rowspan
- td, th

#### selected
- option

#### size
- all inputs, h

#### step
- input_range, input_date

#### style
- all

Adds any inline styles to the element. Pass it using a string as you normally would when writing html.
'style'=>'width:100%; bg-color:blue;' will return style="width:100%; bg-color:blue;"

#### src
- img, embed

#### target
- a

#### title
- all

#### type
- embed, a

#### value
- all inputs, button, checkbox, data, radio

#### width
- all inputs, img, embed

There is also one catchall attribute of 'controls'. This can be used to enter any string into the element so can be used to add any attributes not yet included.


## List of tags and their function names

Only the 'a' tag contains any of the option attributes as an example. All contain examples of attributes that are unique to them.

### a
- 	href, rel, type, target

```php
echo Php2Html::a ([
    'content' => 'Click Me',
    'href' => 'home.php',
    'id' => 'home_button',
    'class' => 'menu',
    'style' => 'display:inline; color:white;',
    'data_attr' => [
        'section' => 'core',
        'order' => '1'
    ]
]);

```

```html
<a href="/home.php" id="home_button" class="menu" style="display:inline; color:white;" data-section="core" data-order="1">Click Me</a><!-- a #home_button .menu -->
```

### abbr

```php
echo Php2Html::abbr ([
    'content' => 'HTML',
    'title' => 'Hyper Text Markup Language',
]);

```

```html
<abbr title="Hyper Text Markup Language">HTML</abbr><!-- abbr -->
```

### address

```php
echo Php2Html::address ([
    'content' => 'Street Name<br>Town Name<br>Country Name',
]);

```

```html
<address>Street Name<br>Town Name<br>Country Name</address><!-- address -->
```

### article

```php
echo Php2Html::article ([
    'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis lectus enim, at imperdiet urna consequat in.</p> <p>Vivamus cursus diam enim, id fermentum metus aliquet eu. Vestibulum eu ex imperdiet eros efficitur posuere in at ipsum. Maecenas orci tortor, scelerisque nec nibh eget, accumsan blandit mi. Sed euismod felis turpis</p>',
]);

```

```html
<article><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis lectus enim, at imperdiet urna consequat in.</p> <p>Vivamus cursus diam enim, id fermentum metus aliquet eu. Vestibulum eu ex imperdiet eros efficitur posuere in at ipsum. Maecenas orci tortor, scelerisque nec nibh eget, accumsan blandit mi. Sed euismod felis turpis</p></article><!-- article -->
```

### aside

```php
echo Php2Html::aside ([
    'content' => '<h4>Lorum Ipsum</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis lectus enim, at imperdiet urna consequat in.</p> <p>Vivamus cursus diam enim, id fermentum metus aliquet eu. Vestibulum eu ex imperdiet eros efficitur posuere in at ipsum. Maecenas orci tortor, scelerisque nec nibh eget, accumsan blandit mi. Sed euismod felis turpis</p>',
]);

```

```html
<aside><h4>Lorum Ipsum</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis lectus enim, at imperdiet urna consequat in.</p> <p>Vivamus cursus diam enim, id fermentum metus aliquet eu. Vestibulum eu ex imperdiet eros efficitur posuere in at ipsum. Maecenas orci tortor, scelerisque nec nibh eget, accumsan blandit mi. Sed euismod felis turpis</p></aside><!-- aside -->
```

### b

```php
echo Php2Html::b ([
    'content' => 'This is bold text',
]);

```

```html
<b>This is bold text</b><!-- b -->
```

### blockquote

```php
echo Php2Html::blockquote ([
    'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    'cite' => 'https://www.lipsum.com/'
]);

```

```html
<blockquote cite="https://www.lipsum.com/">Lorem Ipsum is simply dummy text of the printing and typesetting industry</blockquote><!-- b -->
```

### br
Note that the br() function does not create a htnl comment as 'show_comment' is set to false.
```php
echo Php2Html::br ([
    'content' => '',
]);

```

```html
<br>
```

### button
- form, name, value, disabled, button_type

```php
echo Php2Html::button ([
    'content' => 'Click Me!',
    'button_type' => 'submit',
    'name' => 'submit_button',
    'disabled' => ''
]);
```

```html
<button type="submit" name="submit_button" disabled>Click Me!</button><!-- button -->
```

### checkbox
- checked, name, value, disabled

```php
echo Php2Html::checkbox ([
    'value' => '1',
    'name' => 'check_box',
    'checked' => ''
]);
```

```html
<input type="checkbox" name="check_box" value="1" checked><!-- checkbox -->
```

### cite

```php
echo Php2Html::cite ([
    'content' => 'Citation',
]);
```

```html
<cite>Citation</cite><!-- cite -->
```

### code

```php
echo Php2Html::code ([
    'content' => 'This is some code',
]);
```

```html
<code>This is some code</code><!-- code -->
```

### data

```php
echo Php2Html::data ([
    'content' => 'A piece of data',
    'value' => '457'
]);
```

```html
<data value="457">A piece of data</data><!-- data -->
```

### dd

```php
echo Php2Html::dd ([
    'content' => 'This is a description',
]);
```

```html
<dd>This is a description</dd><!-- dd -->
```

### div

```php
echo Php2Html::div ([
    'content' => '<p>This is a paragraph within a div.</p>'
]);
```

```html
<div><p>This is a paragraph within a div.</p></div><!-- div -->
```

### dl

As a dl tag will contain dd and dl tags, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$dl_content = '<dt> and <dd> elements';
echo Php2Html::dl ([
    'content' => $dl_content
]);
```

```html
<dl>...</dl><!-- dl -->
```

### dt

```php
echo Php2Html::dt ([
    'content' => 'This is a definition'
]);
```

```html
<dt>This is a definition</dt><!-- dt -->
```

### em

```php
echo Php2Html::em ([
    'content' => 'Emphasised text'
]);
```

```html
<em>Emphasised text</em><!-- em -->
```

### fieldset
- form, disabled

As a fieldset tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$fieldset_content = 'form elements';
echo Php2Html::fieldset ([
    'content' => $fieldset_content,
    'form' => 'login_form',
    'name' => 'user_details'
]);
```

```html
<fieldset name="user_details" form="login_form">...</fieldset><!-- fieldset -->
```

### figcaption

```php
echo Php2Html::figcaption ([
    'content' => 'This is a caption',
]);
```

```html
<figcaption>This is a caption</figcaption><!-- figcaption -->
```

### figure

As a figure tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$figure_content = 'figure elements';
echo Php2Html::figure ([
    'content' => $figure_content,
]);
```

```html
<figure>...</figure><!-- figure -->
```

### footer
- action, method

As a footer tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$footer_content = 'footer content';
echo Php2Html::footer ([
    'content' => $footer_content,
]);
```

```html
<footer>...</footer><!-- footer -->
```


### form
- action, method

As a form tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$form_content = 'form elements';
echo Php2Html::form ([
    'content' => $form_content,
    'action' => 'login.php',
    'method' => 'post'
]);
```

```html
<form action="login.php" method="post">...</form><!-- form -->
```

### h
- size

All h tags work from the same function. We passs in a parameter of size to choios from h1, h2 etc.
```php
echo Php2Html::h ([
    'content' => 'Header Text',
    'size' => '2'
]);
```

```html
<h2>Header Text</h2><!-- h -->
```

### header

As a header tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php
$header_content = 'header content';
echo Php2Html::header ([
    'content' => $header_content,
]);
```

```html
<header>...</header><!-- header -->
```
### hr

```php
echo Php2Html::hr ([
    'content' => '',
]);
```

```html
<hr>
```

### i

```php
echo Php2Html::i ([
    'content' => 'Italicised text',
]);
```

```html
<i>Italicised text</i>
```

### img
- alt, height, src, width

```php
echo Php2Html::img ([
    'src' => 'image.gif',
    'alt' => 'My Image',
    'height' => '50',
    'width' => '100',
]);
```

```html
<img src="image.gif" alt="My Image" height="50" width="100"><!-- img -->
```

## input_xxx
Each different input type has it own function to simplify calling them.

We will show the 'text' input first as that can accept most of the attributes. After that, each of the different input functions will only generally show any attributes that have not been shown in the 'text' input and have not already been shown in another input example. Any attributes available for a function will be shown just under the function name, regardless of whether or not they are shown in the example.

In the examples, we will assume all can accept the disabled, form, height, width, name, placeholder, required, size and value attributes so will only show them in the text input example.

### input_text

```php
echo Php2Html::input_text ([
    'name' => 'firstname',
    'value' => 'Steve',
    'placeholder' => 'Enter your first name',
    'required' => '',
    'size' => '40',
    'width'=>'100',
    'height'=>'50'
]);
```

```html
<input type="text"  id="firstname" name="firstname" value="Steve" placeholder="Enter your first name" size="40" width="100" height="50"><!-- input #firstname -->
```


### input_date
- max, min, step

```php
echo Php2Html::input_date ([
    'name' => 'start_date',
    'id' => 'dates',
    'value' => '2018-06-01',
    'min' => '2018-01-01',
    'max' => '2018-12-31',
    'step' => '1',
]);
```

```html
<input type="range"  id="dates" name="start_date" min="2018-01-01" max="2018-12-31" step="1" value="2018-06-01"><!-- input #dates -->
```

### input_email
- multiple

```php
echo Php2Html::input_email ([
    'name' => 'email',
    'id' => 'user_email',
    'value' => 'email@address.com',
    'multiple' => ''
]);
```

```html
<input type="email"  id="user_email" name="email" value="email@address.com" multiple><!-- input #user_email -->
```

### input_hidden
- name, value

```php
echo Php2Html::input_hidden ([
    'name' => 'hidden_field',
    'value' => 'a51',
]);
```

```html
<input type="hidden" name="hidden_field" value="a51"><!-- input -->
```

### input_file
-  multiple

```php
echo Php2Html::input_file ([
    'id' => 'file_upload',
    'name' => 'avatar',
    'multiple'=>''
]);
```

```html
<input type="file" id="file_upload" name="avatar" multiple><!-- input #file_upload -->
```

### input_number
- max, min, step

```php
echo Php2Html::input_number ([
    'name' => 'max_price',
    'id' => 'product',
    'value' => '500',
    'min' => '0',
    'max' => '1000',
    'step' => '50',
]);
```

```html
<input type="number"  id="product" name="max_price" min="0" max="1000" step="50" value="500"><!-- input #product -->
```

### input_password
- minlength, maxlength

```php
echo Php2Html::input_password ([
    'name' => 'password',
    'id' => 'pass',
]);
```

```html
<input type="password"  id="pass" name="password"><!-- input #pass -->
```

### input_range
- max, min, step

```php
echo Php2Html::input_range ([
    'name' => 'max_price',
    'id' => 'product',
    'value' => '500',
    'min' => '0',
    'max' => '1000',
    'step' => '50',
]);
```

```html
<input type="range"  id="product" name="max_price" min="0" max="1000" step="50" value="500"><!-- input #product -->
```

### input_search
- max, min, step

```php
echo Php2Html::input_search ([
    'name' => 'user_search',
    'id' => 'search',
    'value' => '01234 567890',
    'minlength' => '6',
    'maxlength' => '60',
]);
```

```html
<input type="search"  id="search" name="user_search" minlength="6" maxlength="60"><!-- input #search -->
```

### input_tel
- minlength, maxlength

```php
echo Php2Html::input_tel ([
    'name' => 'home_num',
    'id' => 'phone',
    'value' => '01234 567890',
]);
```

```html
<input type="tel"  id="phone" name="home_num" value="01234 567890"><!-- input #phone -->
```

### input_time

```php
echo Php2Html::input_time ([
    'name' => 'current_time',
    'id' => 'current_time',
    'value' => '15:43',
]);
```

```html
<input type="time"  id="current_time" name="current_time" value="15:43"><!-- input #current_time -->
```

### input_url
- minlength, maxlength

```php
echo Php2Html::input_url ([
    'name' => 'home_page',
    'id' => 'link',
    'value' => 'http://www.google.com',
]);
```

```html
<input type="time"  id="link" name="home_page" value="http://www.google.com"><!-- input #link -->
```

### label
-  for, form

```php
echo Php2Html::label ([
    'for' => 'username',
    'form' => 'login',
    'content' => 'Username'
]);
```

```html
<label for="username" form="login">Username</label><!-- label -->
```

### legend

```php
echo Php2Html::legend ([
    'content' => 'Account Details'
]);
```

```html
<legend>Account Details</legend><!-- legend -->
```
### li
- value

```php
echo Php2Html::li ([
    'content' => 'List Content',
    'value' => 'content'
]);
```

```html
<li value="content">List Content</li><!-- li -->
```

### main
As a main tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.

```php
$main_content = 'Main Content';
echo Php2Html::main ([
    'content' => $main_content
]);
```

```html
<main>...</main><!-- main -->
```

### mark

```php
echo Php2Html::mark ([
    'content' => 'Marked Text'
]);
```

```html
<mark>Marked Text</mark><!-- mark -->
```
### nav
As a nav tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.

```php
$nav_content = 'Nav Content';
echo Php2Html::nav ([
    'content' => $nav_content
]);
```

### meta
- content, name

```php
echo Php2Html::meta ([
    'name' => 'name.',
    'content' => 'Steve Ball.',
]);
```

```html
<meta name="name" content="Steve Ball">
```

### ol
As an ol tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.

```php
$ol_content = 'List Content';
echo Php2Html::ol ([
    'content' => $ol_content
]);
```

```html
<ol>...</ol><!-- ol -->
```

### option
- label, disabled, selected

```php
echo Php2Html::option ([
    'content' => 'Option Text',
    'value' => '1'
]);
```

```html
<option value="1">Option Text</option><!-- option -->
```

### optgroup
- label, disabled

As an optgroup tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.

```php
$optgroup_content = 'Group Content';
echo Php2Html::optgroup ([
    'content' => $optgroup_content,
    'label' => 'Categories'
]);
```

```html
<optgroup label="Categories">...</optgroup><!-- optgroup -->
```

### p

```php
echo Php2Html::p ([
    'content' => 'This is some text within a paragraph.'
]);
```

```html
<p>This is some text within a paragraph.</p><!-- p -->
```

### pre

```php
echo Php2Html::pre ([
    'content' => 'This is some preformatted text.'
]);
```

```html
<pre>This is some preformatted text.</pre><!-- pre -->
```

### progress
- max, value

```php
echo Php2Html::progress ([
    'max' => '100',
    'value' => '50'
]);
```

```html
<progress max="100" value="50"></progress><!-- progress -->
```

### radio
- checked, name, value, disabled

```php
echo Php2Html::radio ([
    'value' => '1',
    'name' => 'radio_button',
    'checked' => ''
]);
```

```html
<input type="radio" name="radio_button" value="1" checked><!-- radio -->
```

### s

```php
echo Php2Html::s ([
    'content' => 'Some inaccurate text',
]);
```

```html
<s>Some inaccurate text</s><!-- s -->
```

### samp

```php
echo Php2Html::samp ([
    'content' => 'Sample output',
]);
```

```html
<samp>Sample output</samp><!-- s -->
```

### section

As a section tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$section_content = 'Section Content';
echo Php2Html::section ([
    'content' => $section_content,
]);
```

```html
<section>...</section><!-- section -->
```

### select

As a select tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$options = 'Options';
echo Php2Html::select ([
    'content' => $options,
    'id' => 'select_me',
    'name' => 'dropdown',
    'multiple' => '',
    'required' => '',
]);
```

```html
<select id="select_me" name="dropdown" multiple required>...</select><!-- select #select_me  -->
```

### small

```php
echo Php2Html::small ([
    'content' => 'Sample output',
]);
```

```html
<small>Sample output</small><!-- small -->
```

### span

```php
echo Php2Html::span ([
    'content' => 'Text within a span',
]);
```

```html
<span>Text within a span</span><!-- span -->
```

### strong

```php
echo Php2Html::strong ([
    'content' => 'Important text',
]);
```

```html
<strong>Important text</strong><!-- strong -->
```

### sub

```php
echo Php2Html::sub ([
    'content' => 'Subscript text',
]);
```

```html
<sub>Subscript text</sub><!-- sub -->
```

### sup

```php
echo Php2Html::sup ([
    'content' => 'Superscript text',
]);
```

```html
<sup>Superscript text</sup><!-- sup -->
```

### table

As a table tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$table_content = 'Table Content';
echo Php2Html::select ([
    'content' => $table_content,
]);
```

```html
<table>...</table><!-- table  -->
```

### tbody

As a tbody tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$tbody_content = 'Tbody Content';
echo Php2Html::tbody ([
    'content' => $tbody_content,
]);
```

```html
<tbody>...</tbody><!-- tbody  -->
```

### textarea
- cols, required, form, minlength, maxlength, placeholder, rows

```php
echo Php2Html::textarea ([
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'placeholder' => 'Enter your article details',
    'required' => '',
    'rows' => '4',
    'cols'=>'50',
    'minlength' =>'100',
    'maxlength' =>'1000',
]);
```

```html
<textarea rows="4" cols="50" placeholder="Enter your article details" minlength="100" maxlength="1000" required>
Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</textarea><!-- textarea  -->
```

### td

```php
echo Php2Html::td ([
    'content' => 'Cell Content',
    'colspan' => '2',
    'rowspan' => '3',
]);
```

```html
<td colspan="2" rowspan="3">Cell Content</td><!-- td -->
```

### tfoot

```php
echo Php2Html::tfoot ([
    'content' => 'Table footer content',
]);
```

```html
<tfoot>Table footer content</tfoot><!-- tfoot -->
```

### th

```php
echo Php2Html::th ([
    'content' => 'Header Cell Content',
    'colspan' => '5',
    'rowspan' => '3',
]);
```

```html
<th colspan="5" rowspan="3">Header Cell Content</th><!-- th -->
```

### thead

As a thead tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$thead_content = 'Thead Content';
echo Php2Html::thead ([
    'content' => $thead_content,
]);
```

```html
<thead>...</thead><!-- thead  -->
```

### time
- datetime

```php
echo Php2Html::time ([
    'datetime' => '2018-06-14 19:00',
]);
```

```html
<time datetime="2018-06-14 19:00"><!-- time -->
```

### tr

As a tr tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$tr_content = 'Tr Content';
echo Php2Html::tr ([
    'content' => $tr_content,
]);
```

```html
<tr>...</tr><!-- tr  -->
```

### u

```php


echo Php2Html::u ([
    'content' => 'Stylised text.',
]);
```

```html
<u>Stylised text.</u><!-- u  -->
```

### ul

As a ul tag will contain multiple other elements, it would be best to create these first as a variable and then pass them in as 'content'.
```php

$ul_content = 'Ul Content';
echo Php2Html::ul ([
    'content' => $ul_content,
]);
```

```html
<ul>...</ul><!-- ul  -->
```

## Examples
There are a few example files in the /examples folder. These mainly focus on some of the more complicated ways of using Php2Html and are fully commented to explain the process as it goes along.

All files use [Bootstrap 4](http://getbootstrap.com) and some use [FontAwesome 5](https://fontawesome.com) to give the examples some styling.

**table_builder.php** gives an example of the make_table() function and shows how to create a simple user table with links and criteria specific formatting.

**select_menu.php** shows how to build a form dropdown menu along with preselecting an option and disabling the select.

**nav_menu.php** gives an example of a complex set up by creating a navigation menu, including a home button using a FontAwesome icon and a dropdown menu, as well as using the 'aria' and 'data' attributes, which require arrays.

## About
## Concept
This whole project started off as one simple function to make building &lt;a	&gt; tags simpler in a project I was working on.

While creating a navbar which had static links, dropdowns and other &lt;a&gt; based triggers, I found that most of my code was a jumble of HTML and becoming difficult to navigate. So I built a function to simplify creating links and was very happy with the end result. But obviously the dropdowns needed &lt;div&gt;s, so I created those too. Then of course a &lt;ul	&gt; and &lt;li&gt; to hold the menu items in the dropdown.  

Realising that my functions file was now 80% functions for making HTML tags, but with the fact that my code was now far more readable and navigable, I decided to make it a project in it's own right, as a class that can be used on any site that uses Php.

It may be overkill in places but there are certainly times, particularly with inputs of long lists, that it really makes light work of the task.  

## Version History
#### 0.1.0
Initial Build
