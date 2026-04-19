import path from "path";
import {
  Configuration as WebpackConfiguration,
  HotModuleReplacementPlugin,
} from "webpack";
import { Configuration as WebpackDevServerConfiguration } from "webpack-dev-server";
import HtmlWebpackPlugin from "html-webpack-plugin";
const Dotenv = require("dotenv-webpack");
const ProgressBarPlugin = require("progress-bar-webpack-plugin");

interface Configuration extends WebpackConfiguration {
  devServer?: WebpackDevServerConfiguration;
}

const publicPath = "/";
const projectPath = path.resolve(process.cwd(), "./");

const config: Configuration = {
  mode: "development",
  output: {
    publicPath: publicPath,
  },
  entry: path.join(__dirname, "/src/index.tsx"),
  module: {
    rules: [
      {
        test: /\.(ts|js)x?$/i,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: [
              "@babel/preset-env",
              "@babel/preset-react",
              ["@babel/preset-typescript", { allowDeclareFields: true }],
            ],
          },
        },
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          // Creates `style` nodes from JS strings
          "style-loader",
          // Translates CSS into CommonJS
          "css-loader",
          // Compiles Sass to CSS
          "sass-loader",
        ],
      },
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader"],
      },
      {
        test: /\.(png|jpg|gif|blob|jpeg|dds)$/i,
        type: "asset",
        parser: {
          dataUrlCondition: {
            maxSize: 8192,
          },
        },
        generator: {
          filename: "assets/images/[name].[hash:6][ext]",
        },
      },
      {
        test: /\.(eot|ttf|woff|woff2)$/,
        type: "asset",
        parser: {
          dataUrlCondition: {
            maxSize: 8192,
          },
        },
        generator: {
          filename: "assets/fonts/[hash:6][ext]",
        },
      },
      {
        test: /\.svg$/,
        use: ["@svgr/webpack", "url-loader"],
      },
      {
        test: /\.(mp4)$/i,
        type: "asset",
        parser: {
          dataUrlCondition: {
            maxSize: 8192,
          },
        },
        generator: {
          filename: "assets/videos/[name].[hash:6][ext]",
        },
      },
    ],
  },
  resolve: {
    extensions: ["*", ".tsx", ".ts", ".js"],
    alias: {
      assets: path.resolve(projectPath, "src/assets"),
      src: path.resolve(projectPath, "src"),
      components: path.resolve(projectPath, "src/components"),
      controllers: path.resolve(projectPath, "src/controllers"),
      models: path.resolve(projectPath, "src/models"),
      navigation: path.resolve(projectPath, "src/navigation"),
      types: path.resolve(projectPath, "src/types"),
    },
  },
  plugins: [
    new HtmlWebpackPlugin({
      template: "src/template.html",
    }),
    new Dotenv(),
    new HotModuleReplacementPlugin(),
    new ProgressBarPlugin(),
  ],
  devtool: "inline-source-map",
  devServer: {
    static: path.join(__dirname, "build"),
    historyApiFallback: true,
    port: 4003,
    open: true,
    hot: true,
  },
};

export default config;
