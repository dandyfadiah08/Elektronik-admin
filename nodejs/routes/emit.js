const express = require("express");
// const dotenv = require("dotenv").config();
// if (dotenv.error) throw dotenv.error;
// const env = dotenv.parsed;

const wrapper = (env, io) => {
  var router = express.Router();
  router.post("/emit", (req, res, next) => {
    var response = {
      success: false,
      message: "Unauthorized",
      data: [],
    }, response_code = 401
    // console.log(req);
    console.log(req.headers.authorization);
    // check for basic auth header
    if (
      !req.headers.authorization ||
      req.headers.authorization.indexOf("Basic ") === -1
    ) {
      response.message = "Missing Authorization Header"
    }

    // verify auth credentials
    const base64Credentials = req.headers.authorization.split(" ")[1];
    const credentials = Buffer.from(base64Credentials, "base64").toString(
      "ascii"
    );
    const [username, password] = credentials.split(":");
    console.log(username, password);
    console.log(req.body);
    console.log(password == env.KEY)
    if (password == env.KEY) {
      res_code = 200;
      response.success = true
      response.message = "OK"
      io.emit(req.body.event, req.body.data);
    }

    return res.status(response_code).json(response);
  });

  return router;
};
module.exports = wrapper;
