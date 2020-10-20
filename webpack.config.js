const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
  mode: 'production',
  entry: path.join(__dirname, 'resources/js/app.js'),
  output: {
    path: path.join(__dirname, 'public/js'),
    filename: 'bundle.js',
  },
  module: {
    rules:[
      {
        test: /\.js/,
        exclude: /node_modules/,
        use:[
          {
            loader: 'babel-loader',
          },
        ]
      },
      {
        test: /\.vue$/,
        loader: "vue-loader",
      },
      {
        test: /\.css$/,
        loader: [
          'style-loader',
          'css-loader'
        ]
      }
    ]
  },
  resolve: {
    modules: [`${__dirname}/src`, 'node_modules'],
    extensions: ['.js'],
  },
  plugins: [
    new VueLoaderPlugin()
  ]
}
