<?php
// Include the Php2Html files
include('../Php2Html.php');
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <title>Php2Html : Select Menu Example</title>
    </head>
    <body>
        <?php
        echo Php2Html::h([
            'size' => '1',
            'content' => 'Select Menu Example'
        ]);
        /*
            The select menu that we are going to show is a simple list of numbers.

            Whether you build the array manually or call it from a database, you
            need to have a key=>value pairing for each option, where the key is
            the value that will be passed when submitting the form and the value
            is the text that will be displayed.

            In this case we build a manual array to work with.
         */
        $optionArray = [
    		'1' => 'One',
    		'2' => 'Two',
    		'3' => 'Three',
    	];

        /*
            Now it is time to build the options so we create one manually
            to start  with that will pass '0' and have text of
            'Please select...'.
         */
    	$options = Php2Html::option ([
            'content' => 'Please select...',
            'value' => '0',
        ]);

        /*
            Next we loop through the array and create a new option for each
            one, adding it to $options as we go.
         */
    	foreach ($optionArray as $key => $value) {
    		$options .= Php2Html::option ([
    			'content' => $value,
    			'value' => $key,
    		]);
    	}

        /*
            Now we pass the $options variable into the select() function
            and assign it to a variable called select, ready to pass into
            the form.
            We also give a Bootstrap class of 'form-control'.
         */
    	$select = Php2Html::select ([
    		'content' => $options,
            'class' => 'form-control'
    	]);

        /*
            Finally we pass $select into the form with a couple of Bootstrap
            sizing and positioning classes.
         */
        echo Php2Html::form ([
    		'content' => $select,
            'class' => 'w-50 mx-auto'
    	]);

        echo Php2Html::hr ([]);

        /*
            If we want the select to be unchangeable, we can make it disabled,
            In this example, we set it to the number 3 option and prevent the
            user from changing it.

            We start by creating the $options variable but as we do not need the
            'Please select...' opening option, we just set it to an empty string.
         */
     	$options = '';

        // We now set the key of the option we want selected as $selected
        $selected = '3';

        /*
            This time, as we loop through, we have one extra element being
            passed into the option () function 'is_selected'. This is a
            boolean value to show which option matches the value to be selected.

            Right at the start of the loop, we check to see if the $key matches
            the value in $selected and passes that to a $is_selected variable.
            This is then passed via 'is_selected'.

            Finally, to disable the menu, we add an empty element of 'disabled'
            into the option () function.
         */
        foreach ($optionArray as $key => $value) {

            $is_selected = ($key == $selected) ? true : false ;

            $options .= Php2Html::option ([
                'content' => $value,
                'value' => $key,
                'is_selected' => $is_selected,
                'disabled' => ''
            ]);
        }

        $select = Php2Html::select ([
            'content' => $options,
            'class' => 'form-control'
        ]);

        echo Php2Html::form ([
            'content' => $select,
            'class' => 'w-50 mx-auto'
        ]);
        ?>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

    </body>
</html>
