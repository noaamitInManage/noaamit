<div data-role="header" id="main_header" class="ui-bar-top" >
	 <center><img style="cursor:pointer;" onclick="javascript:location.href='/';" src="/_media/images/inmanageLogo.png" width="100" ></center>
	<a href="#left-panel" data-icon="bars" data-iconpos="notext" data-shadow="false" data-iconshadow="false">Menu</a>
</div>

	<div data-role="panel" id="left-panel" data-theme="d"> <!-- /panel -->
        <ul data-role="listview" data-theme="d">
            <li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
            <li data-role="list-divider">Menu</li>
            <li data-icon="back"><a href="#demo-intro" data-rel="back">Demo intro</a></li>
        </ul>
        <div data-role="collapsible" data-inset="false" data-iconpos="right" data-theme="d" data-content-theme="d">
          <h3>Categories</h3>
          <div data-role="collapsible-set" data-inset="false" data-iconpos="right" data-theme="b" data-content-theme="d">
            <div data-role="collapsible">
              <h3>Bikes</h3>
              <ul data-role="listview">
                <li><a href="#">Road</a></li>
                <li><a href="#">ATB</a></li>
                <li><a href="#">Fixed Gear</a></li>
                <li><a href="#">Cruiser</a></li>
              </ul>
            </div><!-- /collapsible -->
            <div data-role="collapsible">
              <h3>Cars</h3>
              <ul data-role="listview">
                <li><a href="#">SUV</a></li>
                <li><a href="#">Sport</a></li>
                <li><a href="#">Convertible</a></li>
              </ul>
            </div><!-- /collapsible -->
            <div data-role="collapsible">
              <h3>Boats</h3>
              <ul data-role="listview">
                <li><a href="#">Runabout</a></li>
                <li><a href="#">Motorboat</a></li>
                <li><a href="#">Sailboat</a></li>
              </ul>
            </div><!-- /collapsible -->
          </div><!-- /collapsible-set -->
        </div><!-- /collapsible -->
    </div><!-- /panel -->
    
