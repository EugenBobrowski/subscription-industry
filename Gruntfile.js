module.exports = function (grunt) {
	grunt.initConfig({
		less: {
			development: {
				options: {
					compress: true,
					yuicompress: true,
					optimization: 2
					},
				files: {
					"admin/css/options.css": "admin/css/options.less"
				}
			}
		},
		watch: {
			styles: {
				files: ['admin/css/**/*.less'], // which files to watch
				tasks: ['less'],
				options: {
					nospawn: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['watch']);
};