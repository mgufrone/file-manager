<!DOCTYPE html>
<html id="ng-app" ng-app="app" ng-file-drop>
  <head>
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <script type="text/javascript">
      var base={path:"{{base.path}}"};
    </script>
  </head>
  <body>
  <div id="viewport" ng-controller="FileListController">
    <div class="container">
      <div ng-show="type=='grid'" class="grid-view">
        <ul class="items">
          <li ng-repeat="file in files">
            <a href="#"><img src="{{'{{file.url}}'}}"> {{'{{file.basename}}'}}</a>
          </li>
        </ul>
      </div>
      <div ng-show="type=='list'" class="list-view">
        <ul class="items">
          <li ng-repeat="item in items">
            <a href="#"><img src="{{'{{file.url}}'}}"> {{'{{file.basename}}'}}</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  </body>
</html>