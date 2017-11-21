<?php
require_once 'core/init.php';

/*SPECIFIC SELECT FOR 1 RECORD
$user = DB::getInstance()->get('users', array('username', '=' ,'elias'));

if (!$user->count()) {
    echo "No user";
} else {
    echo $user->getFirst()->username;
}

GENERAL QUERIES
$user = DB::getInstance()->query("SELECT * FROM users");

if (!$user->count()) {
    echo "No user";
} else {
    foreach ($user->results() as $user) {
        echo $user->username . "\n";
    }
}

INSERT
$userInsert = DB::getInstance()->insert('users', array(
    'username' => 'elias',
    'password' => 'password',
    'salt' => 'salt',
    'name' => 'Elias Torres'
));

if ($userInsert === TRUE ) {
    echo "Insert completed";
} else {
    echo "Could not complete insert, error: " . $userInsert[2];
}

UPDATE
$userUpdate = DB::getInstance()->update('users', 11, array(
    'password' => 'newpass',
    'username' => 'dale'
));

if ($userUpdate === TRUE ) {
    echo "Update completed";
} else {
    echo "Could not complete update, error: " . $userUpdate[2];
}*/
/*
if (Session::exists('success')) {
    echo Session::flash('success');
}
*/
if (Session::exists('home')){
    echo '<p>' . Session::flash('home') .'</p>';
}

$user = new User();
if ($user->isLoggedIn()) {
?>
    <p>Hello <a href="#"><?php echo escape($user->data()->username); ?></a>!</p>
    <ul>
        <li><a href="logout.php">Log out</a></li>
    </ul>
<?php
} else {
    echo '<p>You need to <a href="login.php">login</a> or <a href="register.php">register</a></p>';
}