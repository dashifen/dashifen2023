const path = require('path')
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const filename = 'dashifen';

module.exports = (env = {production: false}) => ({
  mode: env.production ? 'production' : 'development',
  devtool: env.production ? 'source-map' : 'eval-cheap-module-source-map',
  entry: path.resolve(__dirname, './assets/scripts/' + filename + '.js'),
  output: {
    path: path.resolve(__dirname, './assets'),
    filename: filename + '.min.js',
    publicPath: '/assets/'
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'assets/scripts'),
    }
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        use: 'vue-loader'
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          'css-loader'
        ]
      }
    ]
  },
  plugins: [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      filename: filename + '.vue.css'
    }),
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: 'true',
      __VUE_PROD_DEVTOOLS__: 'true'
    })
  ],
  watchOptions: {
    ignored: ['node_modules/**']
  }
})
