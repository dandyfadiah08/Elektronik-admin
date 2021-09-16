let io;
module.exports = {
	init: function (server, options) {
		io = require("socket.io")(server, options);
		return io;
	},
	get: function () {
        if (!io) {
            throw new Error("must call .init(server, options) before you can call .get_io()");
        }
        return io;
    },
    event: function() {
		io.on("connection", (socket, data) => {
			console.log(`[SOCKET] New Connection: ${socket.id}`);
			socket.join('global');

			socket.on("notification", (data) => {
				// socket.broadcast.emit("notification", data);
				socket.emit("notification", data);
				console.log("[SOCKET] notification", data);
			});

			socket.on("new-data", (data) => {
				socket.emit("new-data", data);
				console.log("[SOCKET] new-data", data);
			});

            socket.on("join", (data) => {
				socket.join(data.room);
				console.log(`[SOCKET] ${socket.id} join ${data.room}`);
			});
		});
	},
};
