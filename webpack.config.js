const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = [{
    output: {
      path: path.resolve(__dirname, 'public'),
      filename: './js/woocommerce-siret-number-checks-public.js',
      libraryTarget: 'var'
    },
    name: 'public',
    entry: './assets/public/js/main-public.js',
    watch: true,
    watchOptions: {
      poll: true
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader"
          }
        },
        {
          test: /\.sc|ass$/,
          use: [
            { loader: MiniCssExtractPlugin.loader },
            { loader: 'css-loader', options: { importLoaders: 1 } },
            { loader: 'postcss-loader', options: { config: { ctx: { cssnano: {}, autoprefixer: {} } } } },
            { loader: 'sass-loader', options: { sourceMap: true } }
          ]
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: 'css/woocommerce-siret-number-checks-public.css',
        // chunkFilename: "[id].css"
      })
    ]
  }, {
    output: {
      path: path.resolve(__dirname, 'admin'),
      filename: './js/woocommerce-siret-number-checks-admin.js',
      libraryTarget: 'var'
    },
    name: 'admin',
    entry: './assets/admin/js/main-admin.js',
    watch: true,
    watchOptions: {
      poll: true
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader"
          }
        },
        {
          test: /\.sc|ass$/,
          use: [
            { loader: MiniCssExtractPlugin.loader },
            { loader: "css-loader" },
            { loader: 'postcss-loader', options: { config: { ctx: { cssnano: {}, autoprefixer: {} } } } },
            { loader: "sass-loader" }
          ]
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: 'css/woocommerce-siret-number-checks-admin.css',
        // chunkFilename: "[id].css"
      })
    ]
}]