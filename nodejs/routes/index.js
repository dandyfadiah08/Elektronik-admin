const express = require("express");

const wrapper = (env) => {
  var router = express.Router();
  router.get("/", (req, res, next) => {
    const url = `${env.HTTP}://${env.DOMAIN}`;
    res.send(`<script>
        alert('Redirecting to ${url}')
        window.location = '${url}'
        </script> `);
  });

  return router;
};
module.exports = wrapper;
