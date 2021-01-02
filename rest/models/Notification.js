const mongoose = require('mongoose');

const PostSchema =mongoose.Schema({
Text:{
    type: String,
    required: true
},
User_id:{
    type: String,
    required: true
},
Movie_id:{
    type: String,
    required: true
},
Sub_id:{
    type: String,
    required: true
}


});


module.exports = mongoose.model('Notification',PostSchema);
