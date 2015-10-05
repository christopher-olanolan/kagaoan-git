var JSON = JSON || {};

JSON.stringify = JSON.stringify || function (o) {
    var t = typeof (o);
    if (t != "object" || o === null) {
        // simple data type
        if (t == "string") o = '"'+o+'"';
        return String(o);
    }
    else {
        // recurse array or object
        var n, v, j = [], x = (o && o.constructor == Array);
        for (n in o) {
            v = o[n]; t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            j.push((x ? "" : '"' + n + '":') + String(v));
        }
        return (x ? "[" : "{") + String(j) + (x ? "]" : "}");
    }
};
