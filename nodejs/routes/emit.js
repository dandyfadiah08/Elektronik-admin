const express = require("express");
// const dotenv = require("dotenv").config();
// if (dotenv.error) throw dotenv.error;
// const env = dotenv.parsed;

const wrapper = () => {
  var router = express.Router();
  router.post("/emit", (req, res, next) => {
    var env = req.app.get("env");
    var io = req.app.get("io");
    var response = {
        success: false,
        message: "Unauthorized",
        data: [],
      },
      response_code = 401;
    // check for basic auth header
    if (
      !req.headers.authorization ||
      req.headers.authorization.indexOf("Basic ") === -1
    ) {
      response.message = "Missing Authorization Header";
    } else {
      // verify auth credentials
      const base64Credentials = req.headers.authorization.split(" ")[1];
      const credentials = Buffer.from(base64Credentials, "base64").toString(
        "ascii"
      );
      const [username, password] = credentials.split(":");
      console.log(req.body, password == env.KEY);
      if (password == env.KEY) {
        console.log("ok");
        response_code = 200;
        response.success = true;
        response.message = "OK";
        response.data = req.body;
        // io.to("global").emit(req.body.event, req.body.data);
        io.emit(req.body.event, req.body.data);
      }
    }

    return res.status(response_code).json(response);
  });

  return router;
};
module.exports = wrapper;
