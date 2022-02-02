// run the following code (choose one based on environment located on "config/index.js"):
// node app.js --local
// node app.js --development
// node app.js --production

const dotenv = require("dotenv").config();
if (dotenv.error) throw dotenv.error;
const env = dotenv.parsed;
// var cors = require("cors");
var app = require("express")();
const fs = require("fs");
const bodyParser = require("body-parser");
const options = {
  key: fs.readFileSync(env.SSL_KEY_PATH),
  cert: fs.readFileSync(env.SSL_CERT_PATH),
  ca: fs.readFileSync(env.SSL_CA_PATH),
};

const https = env.HTTP == "https" ? require("https") : require("http");

var server = https
  .createServer(options, app)
  .listen(env.PORT, "0.0.0.0", () =>
    console.log(
      `[SERVER] Started on ${env.MODE} mode. ${env.HTTP}://${env.DOMAIN}:${env.PORT}`
    )
  );

/* Socket.IO */
const socketio = require("./middleware/socketio");
var io = socketio.init(server, {
  path: '/socket.io',
  cors: {
    origin: env.ALLOWED_CORS.split(','),
  },
});
socketio.event();

/* Express routes */
app.use(bodyParser.json());
app.use(
  bodyParser.urlencoded({
    extended: true,
  })
);
app.set('env', env)
app.set('io', io)
app.use("/", require("./routes")());
var route_emit = require("./routes/emit")();
app.use("/", route_emit);
