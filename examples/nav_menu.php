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

        <title>Php2Html : Nav Menu Example</title>
    </head>
    <body>
        <?php
        echo Php2Html::h([
            'size' => '1',
            'content' => 'Nav Menu Example'
        ]);

        /*
            We are going to build a simple nav menu with some Bootstrap styling.

            The end result will be created using the nav() function but we will
            build the nav items as we go so we start off by creating an empty
            $nav_items variable.
         */
        $nav_items = '';

        /*
            Next we create a home button using a FontAwesome icon and add
            that to the existing $nav_items.

            We will build this one bit by bit so initially just create the
            icon itself.

            We then create the link, including the Bootstrap nav-link class
            and pass the icon in as the content.

            Finally we pass it into an <li> with the Bootstrap nav-item
            class and add that to the existing $nav_items.
         */

        $icon = Php2Html::i([
            'class' => 'fas fa-home ',
        ]);

        $link = Php2Html::a([
            'content' => $icon,
            'class' => 'nav-link',
            'href' => '/'
        ]);

        $nav_items .= Php2Html::li([
            'content' => $link,
            'class' => 'nav-item'
        ]);

        /*
            Next we add both an button and contact button to $nav_items.

            We do them in slightly different ways, with 'about' we will
            build it bit by bit as we did with the home button, but with
            the 'contact' one we will nest it.
         */
        $link = Php2Html::a([
            'content' => 'About',
            'class' => 'nav-link',
            'href' => '/about'
        ]);

        $nav_items .= Php2Html::li([
            'content' => $link,
            'class' => 'nav-item'
        ]);

        $nav_items .= Php2Html::li([
            'content' => Php2Html::a([
                'content' => 'Contact',
                'class' => 'nav-link',
                'href' => '/contact'
            ]),
            'class' => 'nav-item'
        ]);

        /*
            Finally we will add a dropdown menu but as these are
            complex, we will break it down into as small chunks
            as possible.

            The innermost items are the links themselves so we will
            construct those first by putting the items in an array.
         */
        $array = [
            '1' =>['News','/news'],
            '2' =>['Sport','/sport'],
            '3' =>['Weather','/weather'],
            '4' =>['Submit a Story','/news'],
            '5' =>['Tags','/tags']
        ];

        /*
            Next we loop through the array and for each item we create
            a link, with a Bootstrap class, using the first part of the
            array element as the  display text and the second part as
            the href.
            We use a variable called $links and build on this through
            the loop.
         */
        $links = '';

        foreach ($array as $key => $value) {
            $links .= Php2Html::a([
                'content' => $value[0],
                'class' => 'dropdown-item',
                'href' => $value[1]
            ]);

            /*
                We want to insert a divider after the Weather category
                so we check to see if that is where we are in the loop.
                If we are, we add a divider to $links.
             */
            if ($value[0] == 'Weather') {
                $links .= Php2Html::div([
                    'class' => 'dropdown-divider'
                ]);
            }
        }

        /*
            Now we need to place the links inside a <div> with a class
            of dropdown-menu to form the dropdown itself.
         */
        $div = Php2Html::div([
            'content' => $links,
            'class' => 'dropdown-menu'
        ]);

        /*
            We need the link to trigger the dropdown so we create
            that next and pass it into the li () just before the $div.

            The trigger requires some of the attributes that require
            arrays to be passed in. Included in this example are
            'aria-haspopup', 'aria-expanded' and 'data-toggle'.
         */
        $trigger = Php2Html::a([
            'content' => 'Categories',
            'class' => 'nav-link dropdown-toggle',
            'href' => '#',
            'aria' => [
                'haspopup' => 'true',
                'expanded' => 'false'
            ],
            'data_attr' => [
                'toggle' => 'dropdown'
            ],
            'role' => 'button'
        ]);

        $nav_items .= Php2Html::li([
            'content' => $trigger.$div,
            'class' => 'nav-item  dropdown'
        ]);

        /*
            Finally, we pass $nav_items into the nav () function
            and add some Bootstrap styling classes.
         */
        echo Php2Html::nav([
            'content' => $nav_items,
            'class' => 'nav bg-light border border-secondary'
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
