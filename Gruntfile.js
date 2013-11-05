module.exports = function(grunt) {

  grunt.initConfig({
    // pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        separator: "\n;"
      },
      dist:{
        src:[
          'app/lib/assets/jquery/jquery.min.js',
          'app/lib/assets/bootstrap/dist/js/bootstrap.min.js',
          'app/lib/assets/es5-shim/es5-shim.min.js',
          'app/lib/assets/angular/angular.min.js',
          'app/lib/assets/angular-file-upload/angular-file-upload.min.js',
          'app/assets/**/*.js'
        ],
        dest:'assets/file-manager-assets.js'
      },
      css:{
        src:[
        'app/lib/assets/bootstrap/dist/css/bootstrap.min.css',
        'app/lib/assets/bootstrap/dist/css/bootstrap-theme.min.css',
        'app/assets/**/*.css'
        ],
        dest: 'assets/file-manager.css'
      }
    },
    uglify: {
      options: {
        banner: '/*! @author Mochamad Gufron \n @email mgufronefendi@gmail.com \n @package file-manager */\n',
        preserveComments:false
      },
      dist: {
        files: {
          'assets/file-manager.min.js': ['<%= concat.dist.dest %>']
        }
      }
    },
    cssmin: {
      options: {
        banner: '/*! @author Mochamad Gufron \n @email mgufronefendi@gmail.com \n @package file-manager */\n',
        preserveComments:false,
        compress:true,
      },
      dist: {
        files: {
          'assets/file-manager.min.css': ['<%= concat.css.dest %>']
        }
      }
    },
    // qunit: {
    //   files: ['test/**/*.html']
    // },
    // jshint: {
    //   files: ['Gruntfile.js', 'src/**/*.js', 'test/**/*.js'],
    //   options: {
    //     // options here to override JSHint defaults
    //     globals: {
    //       jQuery: true,
    //       console: true,
    //       module: true,
    //       document: true
    //     }
    //   }
    // },
    watch: {
      files: ['<%= concat.dist.src %>','<%= concat.css.src %>'],
      tasks: ['concat', 'uglify','cssmin']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  // grunt.loadNpmTasks('grunt-contrib-jshint');
  // grunt.loadNpmTasks('grunt-contrib-qunit');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // grunt.registerTask('test', ['jshint', 'qunit']);

  grunt.registerTask('default', ['concat', 'uglify','cssmin']);

};