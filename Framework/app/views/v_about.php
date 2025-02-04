<?php require APPROOT.'/views/inc/components/header.php';?>
<h1>Users</h1>

<?php foreach($data['users']as $user):?>
    <p><?php echo $user->name;?>-<?php echo $user->age;?></p>
    <?php endforeach;?>
<?php require APPROOT.'/views/inc/components/footer.php';?>