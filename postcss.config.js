module.exports = ({ options }) => ({
    plugins: {
        'autoprefixer': process.env.NODE_ENV === 'production' ? options.autoprefixer : false,
		'cssnano': process.env.NODE_ENV === 'production' ? options.cssnano : false
    }
})