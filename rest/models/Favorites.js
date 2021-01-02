const mongoose = require('mongoose');

const PostSchema =mongoose.Schema({

User_id: {
    type: String,
    required: true
},
Movie_id:{
    type: String,
    required: true
}

});

module.exports = mongoose.model('Favorites',PostSchema);
