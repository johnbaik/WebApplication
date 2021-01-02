const mongoose = require('mongoose');

const PostSchema =mongoose.Schema({

Cinema_owner: {
    type: String,
    required: true
},
Cinema_name:{
    type: String,
    required: true
}

});


module.exports = mongoose.model('Cinemas',PostSchema);
