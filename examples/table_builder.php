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

        <title>Php2Html : Table Builder Example</title>
    </head>
    <body>
        <?php
        echo Php2Html::h([
            'size' => '1',
            'content' => 'Table Builder Example'
        ]);

        /*
            The table that we are going to build will be a simple
            users table.

            Based on the assumption that the data will have been
            taken from a database, the key for each user is presumed
            the user id and the 'status' is 1 for active member, 0 for
            inactive member and -1 for banned member.

            We will be applying row background colours for the banned
            and inactive members, a different text colour for Admin and
            Standard members, as well as creating links to various user
            pages.
         */
        $array = [
            '1' => [
                'username' => 'SuperAdmin',
                'group' => 'Admin',
                'gender' => 'male',
                'status' => '1'
            ],
            '2' => [
                'username' => 'Admin',
                'group' => 'Admin',
                'gender' => 'female',
                'status' => '1'
            ],
            '3' => [
                'username' => 'RegularUser',
                'group' => 'Standard',
                'gender' => 'female',
                'status' => '1'
            ],
            '4' => [
                'username' => 'BannedUser',
                'group' => 'Standard',
                'gender' => 'male',
                'status' => '-1'
            ],
            '5' => [
                'username' => 'InactiveUser',
                'group' => 'Standard',
                'gender' => 'male',
                'status' => '0'
            ],
        ];

        /*
            Now that we have that list, we need to loop through it and
            add various links to other pages and certain classes when
            criteria is met.
         */
        foreach ($array as $key => $value) {

            /*
                Firstly, we replace the existing 'username' column with
                a link to the users profile page, where their username
                is the url parameter, eg. /Users/Profile/SuperAdmin
             */
            $array[$key]['username'] = Php2Html::a ([
                'content' => $value['username'],
                'href' => '/Users/Profile/'.$value['username']
            ]);

            /*
                Next, we want to set a red tr background for any banned
                users and a yellow one for inactive ones.

                To do this, we simply run a switch statement on 'status'
                and if it matches banned or inactive, we set a tr class.
             */
            switch ($value['status']) {
     			case -1:
     				$array[$key]['tr_class'] = 'table-danger';
     				break;

     			case 0:
     				$array[$key]['tr_class'] = 'table-warning';
     				break;
     		} // switch status

            /*
                We also want set the usergroup to have a text colour of
                green if they are an Admin so, we simply check if that
                is the case in an if statement.
             */
            if($value['group'] == 'Admin') {
                $array[$key]['tr_class'] = 'text-success';
            }

            $array[$key]['edit'] = 'edit';

            /*
                The gender passed in is in lowercase so we simply replace
                whatever it is with a capitalised version.
             */
            $array[$key]['gender'] = ucfirst($value['gender']);

            /*
                Finally, we build an edit column using Font Awesome icons for
                view, edit and delete pages. This time, as it would not be
                visible to front end users, we simply use the user id stored
                as the $key.
             */
            $array[$key]['edit'] = Php2Html::a ([
                'content' => Php2Html::i ([
                    'content' => '',
                    'class' => 'fas fa-search text-info mr-2'
                ]),
                'href' => '/Admin/Users/View/'.$key
            ])
            .Php2Html::a ([
                'content' => Php2Html::i ([
                    'content' => '',
                    'class' => 'fas fa-edit text-success mr-2'
                ]),
                'href' => '/Admin/Users/Edit/'.$key
            ])
            .Php2Html::a ([
                'content' => Php2Html::i ([
                    'content' => '',
                    'class' => 'fas fa-times-circle text-danger mr-2'
                ]),
                'href' => '/Admin/Users/Delete/'.$key
            ]);
        }

        /*
            Now that we have the data formatted and amended the way we
            require, we can send it to the make_table() function, along
            with the rest of the settings we need.
         */
        echo Php2Html::make_table ([
            // Pass in the array as 'data'
            'data' => $array,
            /*
                Specify required columns and styles

                We are going to show three of the for columns that we
                already had, including all the changes made above, as
                well as the edit row that we created.

                We will create an alias to show as the column header for
                username, group and gender, but will show no text in the
                header for the edit column.

                All headers will have a dark background and light text,
                except for edit, which will have a slightly lighter
                background.

                The text in the gender column will be red.

                The edit column will have a light background.
             */
            'columns' => [
                'username' => [
                    'alias' => 'Username',
                    'th_class' => 'bg-dark text-light',
                ],
                'group' => [
                    'alias' => 'User Group',
                    'th_class' => 'bg-dark text-light',
                ],
                'gender' => [
                    'alias' => 'Gender',
                    'th_class' => 'bg-dark text-light',
                    'global_class' => 'text-danger'
                ],
                'edit' => [
                    'alias' => '',
                    'th_class' => 'bg-secondary text-light',
                    'td_class' => 'bg-light'
                ],
            ],
            /*
                Specify table settings

                All that we do here is give the whole table a class of
                'table' for Bootstrap formatting, and set 'show_header'
                to true.
             */
            'settings' => [
                'show_header' => true,
                'class' =>'table mb-0'
            ]
        ]); // make table()
        ?>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

    </body>
</html>
