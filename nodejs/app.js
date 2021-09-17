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
  cors: {
    origin: env.HTTP + "://" + env.DOMAIN,
  },
});
// const io = require("socket.io")(server, {
// 	cors: {
// 		origin: env.HTTP + "://" + env.DOMAIN,
// 	},
// });
socketio.event();
// io.on("connection", (socket, data) => {
// 	console.log(`[SOCKET] New Connection: ${socket.id}`);

// 	socket.on("notifikasi", (data) => {
// 		// socket.broadcast.emit("notifikasi", data);
// 		console.log("[SOCKET] notifikasi", data);
// 	});

// 	socket.on("join", (data) => {
// 		socket.join(data.room);
// 		console.log(`[SOCKET] ${socket.id} join "${data.room}" room`);
// 	});

// });

/* Express routes */
app.use(bodyParser.json());
app.use(
  bodyParser.urlencoded({
    extended: true,
  })
);
app.use("/", require("./routes")(env));
var route_emit = require("./routes/emit")(env, io);
app.use("/", route_emit);
// app.use("/message", router_message);
// app.use('/message', cors(corsOptions), message); // if using cors
