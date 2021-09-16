test('Configuration: local', () => {
    var config = require('./index')();
    expect(config.mode).toBe('local');
});
test('Configuration: development', () => {
    var config = require('./index')('development');
    expect(config.mode).toBe('development');
});
test('Configuration: production', () => {
    var config = require('./index')('production');
    expect(config.mode).toBe('production');
});
// test('Database connection', () => {
//     // masih belum benar
//     var mysql = require('mysql');
//     const database = require('../config/database')();
//     var db = mysql.createConnection({
//         host: database.host,
//         user: database.user,
//         password: database.password,
//         database: database.database
//     });
//     db.connect(function(err) {
//         if(err) throw err;
//         expect(err).toBeNull();
//     })
// });
