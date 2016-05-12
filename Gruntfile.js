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
					"admin/atf-fields/assets/fields.css": "admin/atf-fields/assets/fields.less",
					"admin/css/subscribtion-industry-admin.css": "admin/css/subscribtion-industry-admin.less"
				}
			}
		},
		watch: {
			styles: {
				files: ['admin/**/*.less'], // which files to watch
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