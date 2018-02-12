<?php
/**
 * PHP2HTML
 *
 * A set of Php functions to create HTML tags and other elements
 * within a script, rather than switching betweek Php and HTML.
 *
 * These are fully nestable and can contain all standard attributes
 * such as id's, classes and HTML5 data attributes.
 *
 * Full examples
 * https://github.com/MargateSteve/php2html
 *
 *
 * This file is divide into three sections
 *
 * Build functions
 * These are internal functions which are only used from within the tag functions
 *
 * Tag functions
 * These are the actual functions to be called from other files to generate the tags
 *
 * Other elements
 * Any functions to build elements that go beyound simple tags, such as tables.
 *
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author      Steve Ball <steve@follyball.co.uk>
 * @copyright   Copyright (c) 2017 Steve Ball <steve@follyball.co.uk>
 * @link		https://github.com/MargateSteve/php2html
 * @version		1.0.0
 */
class Php2Html {

    /////////////////////
    // Build functions //
    /////////////////////

    /**
     * make_tag ()
     *
     * This is the core function that builds the tags.
     *
     * This is called from all of the individual tag functions with
     * the 'tag' automatically passed in the $array, along with a 'structure'
     * to denote which structure of tag we are building.
     *
     * A full list of attributes that can be used is shown in the comment at the
     * start of the tags section.
     *
     * NOTE: This is a protected function and can only be called via the tag
     * functions in this file.
     *
     * @param   array   $array  The type of tag and all content/attributes
     * @return  string          Complete HTML tag
     */
    protected static function make_tag ($array) {
        // Set the attributes that have been passed in.
        $attr = self::set_attributes ($array);

        /*
            We automatically add a Html comment to the end of every tag.
            This is generated as we go through the parameters but we start
            it off but creating a $comment variable containing the tag itself.
            By default, comments will be created using the tag type, id and class
            but you can add to this by adding 'comment' when calling the function.
            You can also stop a comment from showing by passing in
            'show_comment' => false on an individual call, or permanently for a
            tag by incliding it in the function (see td() and th()).
         */
         if((!isset($array['show_comment']) || $array['show_comment']))  {
            $attr['comment'] = $array['tag'].$attr['comment'];
         }

        /*
            As there are different types of tag structure that need building in
            different ways, we deal with each type individually.The tag Functions
            that call this pass a 'structure' in $array and this is used to show which type we are building.
            We start off with the most common tag structure which has both start and end tags.
         */
        if($array['structure'] == 'full') {
            $output = '<' . $array['tag'];
            $output .= $attr['attributes'];
            $output .= '>';
            $output .=  (isset($array['content'])) ? $array['content'] : '';
            $output .= '</' . $array['tag'] . '>';
        }  // structure = full

        /*
            Next check is to check for tags that only open
         */
        if($array['structure'] == 'single') {
            $output = '<' . $array['tag'];
            $output .= $attr['attributes'];
            // If we creating a meta tag, the content is actually used as an attribute
            $output .= ($array['tag'] == 'meta') ? ' content="'.$array['content'].'"' : '';

            /*
                Some single tags do not need to self-close so the first
                thing we will do is check to see if this is one of them.
             */
            if (
                $array['tag'] == 'hr' ||
                $array['tag'] == 'br' ||
                $array['tag'] == 'img' ||
                $array['tag'] == 'meta'
            ) {
                $output .= '>';
            } else {
                $output .= '/>';
            }
        } // structure = single

        // If we have anything set as a comment add it to the end of the tag
        if($attr['comment']) {
            $output .= self::htmlComment($attr['comment']);
        }

        // Return the entire tag
        return $output;
    } // make_tag ()

    /**
     * set_attributes ()
     *
     * Sets the attributes and their values for the tag.
     *
     * This is called via the make_tag function and the parameters
     * set in $array are passed in from the original tag function call.
     *
     * @param   array   $array   Attributes and their values
     */
    protected static function set_attributes ($array) {

        /*
            We use an array called $output to collect the data
            to return, so we start of by creating two empty elements,
            one to contain the attributes and one to contain the html
            comment to show at the end.
         */
        $output['attributes'] = '';
        $output['comment'] = '';

        /*
            If we are creating a button, if we want it to have a type other
            than 'button' ('submit' for example) we pass in a parameter
            called button_type, containing the required type.
            If none is set, we simply set the type as 'button'.
            Whichever we use, we add it as a type="" attribute.
         */
        if($array['tag'] == 'button') {
            $type = (isset($array['button_type'])) ? $array['button_type'] : 'button';
            $output['attributes'] .= ' type="' . $type . '"';

        }

        /*
            If 'id' or 'class' parameters have been set, we add them
            as their respective attributes.
            Both of these also make up the html comment so we them to
            that as well.
         */
        if(isset($array['id']) && $array['id']) {
            $output['attributes'] .= ' id="' . $array['id'] . '"';
            $output['comment'] .= ' #' . $array['id'];
        }

        if(isset($array['class']) && $array['class']) {
            $output['attributes'] .= ' class="' . $array['class'] . '"';
            $output['comment'] .= ' .' . $array['class'];
        }

        /*
            Next up, we set any standard attributes that have been requested.

            By standard we mean that they follow the attribute="" format. Any
            other versions of attributes will be added after these.

            We create an array of the standard attributes and loop through
            them, checking each to see if exists in the $array or attributes
            passed in. If it does, it creates the attribute and concatenates
            it to the existing $output['attributes'].
         */
         $standard_attributes = [
 			'action',
 			'alt',
 			'cite',
 			'cols',
 			'colspan',
            'datetime',
 			'for',
 			'form',
 			'height',
            'href',
 			'label',
 			'method',
 			'min',
 			'minlength',
 			'max',
 			'maxlength',
 			'name',
 			'placeholder',
 			'ref',
 			'rows',
 			'rowspan',
 			'step',
 			'src',
 			'target',
 			'title',
 			'type',
 			'value',
 			'width',
         ];

         // Loop through the standard attributes
        foreach ($standard_attributes as $value) {
            // If the attribute exists in array, add it to the existing attributes
            if(isset($array[$value])) {
                $output['attributes'] .= ' ' . $value . '="' . $array[$value] . '"';
            }
        }


        /*
            Next up we add any HTML5 data attributes as well as any aria
            meta data that has been set. Both work the same way, in that
            each takes an array of key=>value pairings where the key is
            the part of the attribute name after the -, and value is the
            attribute value.

            For both, we simply loop through add add an attribute for each
            element each one contains.
         */
        if(isset($array['data_attr'])) {
            foreach ($array['data_attr'] as $key => $value) {
                $output['attributes'] .= ' data-' . $key . '="' . $value . '"';
            }
        }

        if(isset($array['aria'])) {
            foreach ($array['aria'] as $key => $value) {
                $output['attributes'] .= ' aria-' . $key . '="' . $value . '"';
            }
        }

        /*
            If there are any required attrubtes not yet avaiable in this file,
            you can just add them as a string and passing it in as 'controls'
         */
        if(isset($array['controls'])) {
            $output['attributes'] .= ' ' . $array['controls'];
        }

        // Add any inlie style that has been passed in.
        if(isset($array['style'])) {
            $output['attributes'] .= ' style="' . $array['style'].'"';
        }

        /*
            Finally we have a set of non-standard attributes that simply
            need to be added raw, without a value.

            We deal with these in exactly the same way as we did with the
            standard ones.
         */

        $non_standard_attributes = [
            'selected', 'required', 'multiple', 'checked', 'disabled'
        ];

        foreach ($non_standard_attributes as $value) {
            if(isset($array[$value])) {
                $output['attributes'] .= ' ' . $value;
            }
        }

        // Return the attributes as a string
        return $output;
    } // set_attributes

	/**
	 * Create a HTML comment
	 * @param  string 	$details 	The text to place in the comment
	 * @return string           	HTML comment
	 */
	protected static function htmlComment ($details) {
        return '<!-- ' . $details . ' -->';
    }


    ///////////////////
    // Tag functions //
    ///////////////////

    /**
     * These are the individually functions that would be called in the script.
     * The tag itself and the type of structure (used to know which type of tag to
     * build in make_tag()) are automatically passed in.
     * All other required parameters should be passed in when calling the tag function.
     *
     * The most used $params value is 'content'. This is what is actually displayed (the
     * content of a div ot the text of a link).
     *
     * Beyond that, there are a range of other values that can be used in $params. These
     * are mainly standard HTML attributes but there are a couple of variations on this.
     *
     * In this early version, these are not necessarily only usable within tags that they
     * apply to so, although nothing will break, using the wrong thing in the wrong
     * place will not comply with HTML standards.
     *
     * Each tag function explains which attributes it can
     * accept, but assume that all can utilise the global attributes of id, class, title,
     * data-xxx and ref.
     */

    // a           <a>
    // href, rel, type, target
    public static function a ($params) {
        //TODO: Add attributes download
        $params['tag'] = 'a';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // abbr        <abbr>
    public static function abbr ($params) {
        $params['tag'] = 'abbr';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // address       <address>
    public static function address ($params) {
        $params['tag'] = 'address';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // article       <article>
    public static function article ($params) {
        $params['tag'] = 'article';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // aside        <aside>
    public static function aside ($params) {
        $params['tag'] = 'aside';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // b            <b>
    public static function b ($params) {
        $params['tag'] = 'b';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // blockquote   <blockquote>
    // cite
    public static function blockquote ($params) {
		$params['tag'] = 'blockquote';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // br           <br>
    public static function br ($params) {
        $params['tag'] = 'br';
        $params['structure'] = 'single';
        $params['show_comment'] = false;
        return self::make_tag ($params);
    }

    // button       <button>
    // form, name, value, disabled, button_type
    public static function button ($params) {
        /*
            Notes
            'disabled' does not require a value ('disabled'=>'')

			'button_type' can be set when calling to fill
            the 'type=""' attribute with any of the following
            values - button, reset, submit.
            If not set it will default to 'type="button"'
         */
        $params['tag'] = 'button';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // checkbox     <checkbox>
    // checked, name, value, disabled
    public static function checkbox ($params) {
        /*
            Note
            'disabled' does not require a value ('disabled'=>'')
            'checked' does not require a value ('checked'=>'')
         */
         $params['tag'] = 'input';
         $params['type'] = 'checkbox';
         $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // cite         <cite>
    public static function cite ($params) {
        $params['tag'] = 'cite';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // code         <code>
    public static function code ($params) {
        $params['tag'] = 'code';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // data           <data>
    // value
    public static function data ($params) {
        $params['tag'] = 'data';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // dd           <dd>
    public static function dd ($params) {
        $params['tag'] = 'dd';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // div           <div>
    public static function div ($params) {
        $params['tag'] = 'div';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // dl           <dl>
    public static function dl ($params) {
        $params['tag'] = 'dl';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // dt           <dt>
    public static function dt ($params) {
        $params['tag'] = 'dt';
        $params['structure'] = 'full';

        return self::make_tag ($params);
    }

    // em           <em>
    public static function em ($params) {
        $params['tag'] = 'em';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // fieldset     <fieldset>
    // form, disabled
    public static function fieldset ($params) {
        /* Note
			disabled - no value required ('disabled'=>'')
         */
        $params['tag'] = 'fieldset';
        $params['structure'] = 'full';

        return self::make_tag ($params);
    }

    // figcaption   <figcaption>
    public static function figcaption ($params) {
        $params['tag'] = 'figcaption';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // figure       <figure>
    public static function figure ($params) {
        $params['tag'] = 'figure';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // footer       <footer>
    public static function footer ($params) {
        $params['tag'] = 'footer';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // form         <form>
    // action, method
    public static function form ($params) {

        $params['tag'] = 'form';
        $params['structure'] = 'full';

        return self::make_tag ($params);
    }


    // h            <h1> <h2> <h3> <h4>
    public static function h ($params) {
        /*
            The size of the 'h' is passed in as 'size' in the $params so
            initially we just set the tag to 'h'. We then append the size if exists, otherwsie default to 1 (<h1>).
         */
        $params['tag'] = 'h';
        $params['tag'] .= isset($params['size']) ? $params['size'] : '1';
        $params['structure'] = 'full';
        $params['show_comment'] = false;
        return self::make_tag ($params);
    }

    // header       <header>
    public static function header ($params) {
        $params['tag'] = 'header';
        $params['structure'] = 'full';

        return self::make_tag ($params);
    }

    // hr           <hr>
    public static function hr ($params) {
        $params['tag'] = 'hr';
        $params['structure'] = 'single';
        $params['show_comment'] = false;
        return self::make_tag ($params);
    }

    // i            <i>
    public static function i ($params) {
        $params['tag'] = 'i';
        $params['structure'] = 'full';
        $params['show_comment'] = false;
        return self::make_tag ($params);
    }

    // img          <img>
    // alt, height, src, width
    public static function img ($params) {
        $params['tag'] = 'img';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // input_date    <input type="date">
    // disabled, form, height, width, name, placeholder, required, size, value, step, min, max
    public static function input_date ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'date';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_email    <input type="email">
    // disabled, form, height, width, name, placeholder, required, size, value, multiple
    public static function input_email ($params) {
        /*  Note
            'disabled' does not require a value ('disabled'=>'')
            'required' does not require a value ('required'=>'')
            'multiple' does not require a value ('multiple'=>'')
         */
        $params['tag'] = 'input';
        $params['type'] = 'email';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_hidden <input type="input_hidden">
    // name, value
    public static function input_hidden ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'hidden';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_file    <input type="file">
    // multiple
    public static function input_file ($params) {
        /*  Note
            'multiple' does not require a value ('multiple'=>'')
         */
        $params['tag'] = 'input';
        $params['type'] = 'file';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_number    <input type="number">
    // disabled, form, height, width, name, placeholder, required, size, value, max, min, step
    public static function input_number ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'number';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_search    <input type="search">
    // disabled, form, height, width, name, placeholder, required, size, value, minlength, maxlength
    static function input_password ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'password';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_range    <input type="range">
    // disabled, form, height, width, name, placeholder, required, size, value, max, min, step
    public static function input_range ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'range';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_search    <input type="search">
    // disabled, form, height, width, name, placeholder, required, size, value, minlength, maxlength
    public static function input_search ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'search';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_tel    <input type="tel">
    // disabled, form, height, width, name, placeholder, required, size, value, minlength, maxlength
    public static function input_tel ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'tel';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_text   <input type="text">
    // disabled, form, height, width, name, placeholder, required, size, value
    public static function input_text ($params) {
        /*  Note
            'disabled' does not require a value ('disabled'=>'')
            'required' does not require a value ('required'=>'')
         */
        $params['tag'] = 'input';
        $params['type'] = 'text';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_time   <input type="time">
    // disabled, form, height, width, name, placeholder, required, size, value
    public static function input_time ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'time';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // input_url    <input type="url">
    // disabled, form, height, width, name, placeholder, required, size, value, minlength, maxlength
    public static function input_url ($params) {
        $params['tag'] = 'input';
        $params['type'] = 'url';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // label         <label>
    // for, form
    public static function label  ($params) {
        $params['tag'] = 'label';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // legend        <legend>
    public static function legend ($params) {
        $params['tag'] = 'legend';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // li            <li>
    // value
    public static function li ($params) {
	    $params['tag'] = 'li';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // main          <main>
    public static function main ($params) {
        $params['tag'] = 'main';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // mark          <mark>
    public static function mark ($params) {
        $params['tag'] = 'mark';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // meta           <meta>
    // content, name
    public static function meta ($params) {
        $params['tag'] = 'meta';
        $params['structure'] = 'single';
        $params['show_comment'] = false;

        return self::make_tag ($params);
    }

    // nav           <nav>
    public static function nav ($params) {
        $params['tag'] = 'nav';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // ol            <ol>
    public static function ol ($params) {
        $params['tag'] = 'ol';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }


    // option        <option>
    // label, disabled, selected
    public static function option  ($params) {
        /*
            To define which option in a select list is
            selected, pass in 'is_selected' as a parameter.
         */
        $params['tag'] = 'option';
        $params['structure'] = 'full';
		if(isset($params['is_selected']) && $params['is_selected']) {$params['selected'] = '';}
        return self::make_tag ($params);
    }

    // optgroup      <optgroup>
    // label, disabled
    public static function optgroup  ($params) {
        $params['tag'] = 'optgroup';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // p             <p>
    public static function p ($params) {
        $params['tag'] = 'p';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // pre          <pre>
    public static function pre ($params) {
        $params['tag'] = 'pre';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // progress     <progress>
    // max, value
    public static function progress  ($params) {
        $params['tag'] = 'progress';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // progress     <progress>
    // checked, name, value, disabled
	public static function radio ($params) {
        /*
            Note
            'disabled' does not require a value ('disabled'=>'')
            'checked' does not require a value ('checked'=>'')
         */
        $params['tag'] = 'input';
        $params['type'] = 'radio';
        $params['structure'] = 'single';
        return self::make_tag ($params);
    }

    // s            <s>
    public static function s ($params) {
        $params['tag'] = 's';
        $params['structure'] = 'full';

        return self::make_tag ($params);
    }

    // samp         <samp>
    public static function samp ($params) {
        $params['tag'] = 'samp';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // section      <section>
    public static function section ($params) {
        $params['tag'] = 'section';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // select       <select>
    // required, form, multiple, size, disabled
    public static function select  ($params) {
        /*
			To populate a select menu, pass the details for the options in as a key/value array in 'content'
		 */
        $params['tag'] = 'select';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // small        <small>
    public static function small ($params) {
        $params['tag'] = 'small';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // span         <span>
    public static function span ($params) {
        $params['tag'] = 'span';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // strong       <strong>
    public static function strong ($params) {
        $params['tag'] = 'strong';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // sub          <sub>
    public static function sub ($params) {
        $params['tag'] = 'sub';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // sup          <sup>
    public static function sup ($params) {
        $params['tag'] = 'sup';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // table       <table>
    public static function table   ($params) {
        $params['tag'] = 'table';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // tbody        <tbody>
	public static function tbody   ($params) {
		$params['tag'] = 'tbody';
		$params['structure'] = 'full';
		return self::make_tag ($params);
	}

    // textarea    <textarea>
    // cols, required, form, minlength, maxlength, placeholder, rows
    public static function textarea   ($params) {
        $params['tag'] = 'textarea';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // td           <td>
    // colspan, rowspan
	public static function td   ($params) {
		$params['tag'] = 'td';
		$params['structure'] = 'full';
        $params['show_comment'] = false;
		return self::make_tag ($params);
	}

    // tfoot         <tfoot>
	public static function tfoot   ($params) {
		$params['tag'] = 'tfoot';
		$params['structure'] = 'full';

		return self::make_tag ($params);
	}

    // th           <th>
    // colspan, rowspan
	public static function th   ($params) {
		$params['tag'] = 'th';
		$params['structure'] = 'full';
        $params['show_comment'] = false;

		return self::make_tag ($params);
	}

    // th           <thead>
	public static function thead   ($params) {
		$params['tag'] = 'thead';
		$params['structure'] = 'full';
		return self::make_tag ($params);
	}

    // time         <time>
    // datetime
    public static function time ($params) {
        $params['tag'] = 'time';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }

    // tr           <tr>
	public static function tr   ($params) {
		$params['tag'] = 'tr';
		$params['structure'] = 'full';
        $params['comment'] = false;
		return self::make_tag ($params);
	}

    // u            <u>
    public static function u ($params) {
        $params['tag'] = 'u';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }
    // ul           <ul>
    public static function ul ($params) {
        $params['tag'] = 'ul';
        $params['structure'] = 'full';
        return self::make_tag ($params);
    }


    ////////////////////
    // Other elements //
    ////////////////////

	/**
	 * Create a HTML table
	 *
	 * Takes an array and relevant parameters and creates a
	 * full HTML table.
	 *
	 * There are three different parameters that can be passed
	 * in, but each of these are an array containing other
	 * parameters.
	 *
	 * 'data' - contains the records to be placed in the table rows.
	 *
	 * 'columns' - contains a seperate array for each column to be
	 * shown, as well as any classes to be applied to either the th,
	 * td or both.
	 * The key for each column will the database column name. The other
	 * parameters that can be used are
	 * 		'alias' - if the table has a header, this will be the name
	 * 		shown in the th.
	 * 		'td_class' - a class to be added to the columns td.
	 * 		'th_class' - a class to be added to the columns th.
	 * 		'global_class' - a class to be added to both the th and td.
	 *
	 * 'settings' - contains anything relating to the table itself,
	 * such as whether to show a header and any table styles. The
	 * parameters that can be used are
	 * 		'show_header' - true/false to set whether to show the thead.
	 * 		'border' - adds a border to the table with a specified width.
	 * 		'class' - adds the specified class(es) to the whole table.
	 *
	 * You can also add a class to a table row by adding a 'tr_class'
	 * element to the relevant record in the array.
	 *
	 * @param  array 	$params 	Content and settings
	 * @return string         		HTML table
	 */
    public static function make_table ($params) {

		// CReate empty variables to build the relevant sections
		$table_head = '';
        $body_rows = '';

		// Loop through the record data
        foreach ($params['data'] as $rows => $row) {
			/*
				On each loop '$row' will contain the data for each of
				the columns. We start of by adding an empty 'content'
				element to it as will be apply the columns to that
				with any formatting required.

				We build the head and body in the same loop so we
				als create an empty variabkle to hold the data for the head.
			 */
			$row['content'] = '';
			$thead = '';

			/*
				We now loop through the columns array that was passed in.

				We build the th's and td's here so we begin by creating an
				array for each of them including empty 'content' and 'class'
				elements. We then add to each of them as we go through.
			 */
            foreach ($params['columns'] as $columns => $column) {
                $td_params = ['content'=>'', 'class'=>''];
                $th_params = ['content'=>'','class'=>''];

				// Add any global classes to both th and td
                if (isset($column['global_class'])) {
                    $th_params['class'] = $column['global_class'];
                    $td_params['class'] = $column['global_class'];
                } // global class

				// Add any td classes to the td
                if (isset($column['td_class'])) {
                    $td_params['class'] = $td_params['class'] . ' ' . $column['td_class'];
                } // td class

				/*
					If we require a table head, we now create the content for the th and
					add any required classes
				 */
                if(isset($params['settings']['show_header']) && $params['settings']['show_header']) {
					/*
						If an alias has been specified, use that as the content for the th,
						otherwise use the column name.
					 */
					$th_params['content'] = (isset($column['alias'])) ? $column['alias'] : $columns;

					// Add any th classes to the th
					if (isset($column['th_class'])) {
                        $th_params['class'] = $th_params['class'] . ' ' . $column['th_class'];
                    } // th_class

					/*
						Pass the th parameters into the th function and add the completed th
						to $table_head
					 */
                    $table_head .= self::th (
                        $th_params
                    );

                } // head

				/*
					At this point, $row[$columns] contains the content for the td, so we simply
					'content' element of $td_params.
				 */
                $td_params['content'] = $row[$columns];

				/*
					Finally pass the $td_params into the td function and add it to the 'content'
					element for $row.
				 */
                $row['content'] .= self::td (
                    $td_params
                );

            } // foreach $params['columns']

			// Remove the 'show_header' element to prevent conflicts
			unset($params['settings']['show_header']);

			// If there is a tr class set, add it to the row class
            if(isset($row['tr_class']))
            {
                $row['class'] = $row['tr_class'];
            }

			// Pass the row into the tr function and add it to $body_rows
            $body_rows .= self::tr (
                $row
            );
        } // foreach $params['data']

		/*
		 	If $table_head is not empty, add the content it contains to the
			existig $thead variable by placing it in a tr via the tr function
			and that into the thead function
		 */
        if ($table_head) {
            $thead .= self::thead ([
                'content'=> self::tr ([
                    'content'=> $table_head,
                ])
            ]);
        } // if $table_head

		/*
			Finally create the table body as $tbody by passing $body_rows
			 into the tbody function
		 */
        $tbody = self::tbody ([
			'content'=> $body_rows,
		]);// $tbody

		/*
			As eveything else we need for the table, such as the class is stored
			in $params['settings'], we add our content as a new element in that
			including whatever is stored in $thead and $tbody
		 */
        $params['settings']['content'] = $thead.$tbody;

		// Pass $params['settings'] into the table function and return it
        return self::table ($params['settings']);
    } // make_table ()


}
