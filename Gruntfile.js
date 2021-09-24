module.exports = function( grunt ) {
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		sass: {
			dist: {
				options: {
					sourcemap: false,
					compress: false,
					yuicompress: false,
					style: 'expanded',
				},
				files: {
					'assets/css/front-end.css': 'assets/css/front-end.scss',
				},
			},
		},
		watch: {
			css: {
				files: [ 'assets/css/front-end.scss' ],
				tasks: ['sass'],
			},
		},
	} );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.registerTask( 'default', ['sass'] );
};