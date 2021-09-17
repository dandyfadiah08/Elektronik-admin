var express = require("express");
var fun = require("../middleware/common_function");
var message_function = require("../middleware/message_function");
var model = require("../middleware/model");
var config = require("../config")();

var wrapper = (client, db, io) => {
    var router = express.Router();
    
	router.post("/send", function (req, res) {
        var output = {error: true, message: 'No response', data: null};
		console.log("message/send", req.body);
		if (fun.is_empty_param(req.body.key_api, config.key_api)) {
            output.message = 'Wrong API key!';
            res.json(output);
		} else if (fun.is_empty_param(req.body.api)) {
            output.message = 'API is empty!';
            res.json(output);
		} else if (fun.is_empty_param(req.body.number)) {
            output.message = 'Number is empty!';
            res.json(output);
		} else if (fun.is_empty_param(req.body.message)) {
            output.message = 'Message is empty!';
            res.json(output);
		} else {
			if (req.body.type === "image") {
				if (fun.is_empty_param(req.body.url)) {
                    output.message = 'URL is empty!';
                    res.json(output);
				} else if (fun.is_empty_param(req.body.filename)) {
                    output.message = 'Filename is empty!';
                    res.json(output);
				} else {
					var filename = fun.create_filename(req.body.filename);
					var uploadPath = `${config.upload_path}image/${filename}`;
					message_function.download(req.body.url, uploadPath)
                    .then((fileInfo) => {
                        // console.log(fileInfo); // { mime: 'application/pdf', size: 124453 }
                        message_function.send_image(
                            client,
                            req.body.number,
                            req.body.message,
                            uploadPath
                        )
                        .then((msg) => {
                            // console.log('[WA] message_function.send_image SUCC:', msg)
                            msg.api = req.body.api; // add api value to msg
                            msg.filename = filename; // add filename value to msg
                            model.message_insert(db, msg);
                            output.error = false;
                            output.message = 'OK';
                            output.data = {id: msg.id.id, filename: filename};
                            res.json(output);
                        })
                        .catch((e) => {
                            console.log("[WA] message_function.send_image !ERR:", e);
                            output.message = e;
                            output.data = {filename: filename};
                            res.json(output);
                        });
                    })
                    .catch((e) => {
                        output.message = e;
                        output.data = {filename: filename};
                        res.json(output);
                    });
				}
			} else if (
				req.body.type === "document" ||
				req.body.type === "image_as_document"
			) {
				if (fun.is_empty_param(req.body.url)) {
                    output.message = 'URL is empty!';
                    res.json(output);
				} else if (fun.is_empty_param(req.body.filename)) {
                    output.message = 'Filename is empty!';
                    res.json(output);
				} else {
					var filename = fun.create_filename(req.body.filename);
					var uploadPath = `${config.upload_path}document/${filename}`;
					message_function.download(req.body.url, uploadPath)
                    .then((fileInfo) => {
                        if (req.body.type === "image_as_document") {
                            message_function.send_image_as_document(
                                client,
                                req.body.number,
                                req.body.message,
                                uploadPath
                            )
                            .then((msg) => {
                                // console.log('[WA] message_function.send_image_as_document SUCC:', msg)
                                msg.api = req.body.api; // add api value to msg
                                msg.filename = filename; // add filename value to msg
                                model.message_insert(db, msg);
                                output.error = false;
                                output.message = 'OK';
                                output.data = {id: msg.id.id, filename: filename};
                                res.json(output);
                            })
                            .catch((e) => {
                                console.log(
                                    "[WA] message_function.send_image_as_document !ERR:",
                                    e
                                );
                                output.message = e;
                                output.data = {filename: filename};
                                res.json(output);
                            });
                        } else {
                            message_function.send_document(
                                client,
                                req.body.number,
                                req.body.message,
                                uploadPath
                            )
                            .then((msg) => {
                                // console.log('[WA] message_function.send_document SUCC:', msg)
                                msg.api = req.body.api; // add api value to msg
                                msg.filename = filename; // add filename value to msg
                                model.message_insert(db, msg);
                                output.error = false;
                                output.message = 'OK';
                                output.data = {id: msg.id.id, filename: filename};
                                res.json(output);
                            })
                            .catch((e) => {
                                console.log("[WA] message_function.send_document !ERR:", e);
                                output.message = e;
                                output.data = {filename: filename};
                                res.json(output);
                            });
                        }
                    })
                    .catch((e) => {
                        // console.log(e);
                        output.message = e;
                        output.data = {filename: filename};
                        res.json(output);
                    });
				}
			} else {
               message_function.send_chat(client, req.body.number, req.body.message)
               .then((msg) => {
                   output.error = false;
                   output.message = 'OK';
                   output.data = {id: msg.id.id};
                   msg.api = req.body.api; // add api value to msg
                   model.message_insert(db, msg);
                   res.json(output);
               }).catch((e) => {
                   output.message = e;
                   res.json(output);
               })
            }
        }
	});

	router.post("/get_info", function (req, res) {
		console.log("message/get_info", req.body);
        var output = {error: true, message: 'No response', data: null};
		if (fun.is_empty_param(req.body.key_api, config.key_api)) {
            output.message = 'Wrong API key!';
            res.json(output);
		} else if (fun.is_empty_param(req.body.api)) {
            output.message = 'API is empty!';
            res.json(output);
		} else if (fun.is_empty_param(req.body.number)) {
            output.message = 'Number is empty';
            res.json(output);
		} else if (fun.is_empty_param(req.body.id)) {
            output.message = 'ID is empty';
            res.json(output);
		} else {
			const { Message } = require("whatsapp-web.js");
			var msg = new Message(client, {
				id: { _serialized: `true_${req.body.number}@c.us_${req.body.id}` },
			});
			// console.log(msg);
			msg.getInfo()
            .then((messageInfo) => {
                // console.log('msg.getInfo()', messageInfo)
                var ack = -1;
                output.message = 'No Changes';
                if (messageInfo.played.length > 0) {
                    ack = 4;
                    output.message = 'Read'; // Played
                } else if (messageInfo.read.length > 0) {
                    ack = 3;
                    output.message = 'Read';
                } else if (messageInfo.delivery.length > 0) {
                    ack = 2;
                    output.message = 'Delivered';
                }
                if(ack > -1) model.message_update(db, req.body.id, `ack=${ack}`, {
                    io: io,
                    data: { id: req.body.id, ack: ack },
                });
                output.data = ack;
                output.error = false;
                res.json(output);
            })
            .catch((e) => {
                console.log("msg.getInfo() error", e);
                output.message = e;
                res.json(output);
            });
        }
	});

	return router;
};
module.exports = wrapper;
