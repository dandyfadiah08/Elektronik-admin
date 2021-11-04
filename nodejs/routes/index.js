const express = require("express");

const wrapper = () => {
  var router = express.Router();
  router.get("/", (req, res, next) => {
    var env = req.app.get('env');
    const url = `${env.HTTP}://${env.DOMAIN}`;
    res.send(`<script>
        alert('Redirecting to ${url}')
        window.location = '${url}'
        </script> `);
  });

  return router;
};
module.exports = wrapper;
