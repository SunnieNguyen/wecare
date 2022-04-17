window.yasrTrueFalseStringConvertion = function (string) {

    if (typeof string === 'undefined' || string === null || string === '') {
        string = false;
    }

    //Convert string to boolean
    if (string === 'true' || string === '1') {
        string = true;
    }
    if (string === 'false' || string === '0') {
        string = false;
    }

    return string;

}