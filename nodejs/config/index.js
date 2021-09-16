var config = {
  model: process.env.MODE,
  http: process.env.HTTP,
  host: process.env.HOST,
  port: process.env.PORT,
  key_api: process.env.KEY,
};
module.exports = function () {
  return config;
};
