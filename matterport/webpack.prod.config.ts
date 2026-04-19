import path from "path";
import { Configuration } from "webpack";
import HtmlWebpackPlugin from "html-webpack-plugin";
import { CleanWebpackPlugin } from "clean-webpack-plugin";
const Dotenv = require("dotenv-webpack");
const ProgressBarPlugin = require("progress-bar-webpack-plugin");

const publicPath = "/";
const projectPath = path.resolve(process.cwd(), "./");

const config: Configuration = {
  mode: "production",
  entry: path.join(__dirname, "/src/index.tsx"),
  output: {
    path: path.resolve(__dirname, "build"),
    // filename: "[name].[contenthash].js",
    filename: "bundle.js",
    publicPath: publicPath,
  },
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
      favicon: "src/favicon.ico",
    }),
    new Dotenv(),
    new ProgressBarPlugin(),
    // new ForkTsCheckerWebpackPlugin({
    //   async: false,
    // }),
    // new ESLintPlugin({
    //   extensions: ["js", "jsx", "ts", "tsx"],
    // }),
    // new CleanWebpackPlugin(),
  ],
};

export default config;
