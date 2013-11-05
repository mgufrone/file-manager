<!DOCTYPE html>
<html ng-app="myApp">
  <head>
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->

  </head>
  <body>
  <div id="viewport">
    <div class="container" ng-controller="FileListController">
      <div ng-show="grid_view" class="grid-view">
        <ul class="items">
          <li ng-repeat="item in items">
            <a href=""></a>
          </li>
        </ul>
      </div>
      <div ng-show="list_view" class="list-view">
        
      </div>
    </div>
  </div>
  </body>
</html>