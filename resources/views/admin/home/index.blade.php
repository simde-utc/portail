<style>
.home-menu{list-style:none;margin:0;padding:0}.home-menu li{position:relative;list-style:none;margin:0;padding:0}.home-menu li>a{padding:5px 5px 5px 10px;display:block}.home-menu li>a>.fa,.home-menu li>a>.glyphicon,.home-menu li>a>.ion{width:20px}.home-menu li .label,.home-menu li .badge{margin-top:3px;margin-right:5px}.home-menu li.header{padding:10px 25px 10px 15px;font-size:12px}
</style>

<ul class="home-menu">
    @each('admin.home.menu', Admin::menu(), 'item')
</ul>
