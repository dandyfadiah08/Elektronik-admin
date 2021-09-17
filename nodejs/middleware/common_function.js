module.exports = {
    file_ext_split: (filename) => {
        var split = filename.split('.');
        if(split.length <= 1) return ['', filename];
        else return [split.pop(), split.join('.')];
        //return [ext, filename (w/o ext)]
    },
    is_empty_param: (param, match = null) => {
        // console.log(param, match, param !== match, param === undefined, param === '')
        if(match) return param !== match || param === undefined || param === ''
        return param == undefined || param === ''
    }
}