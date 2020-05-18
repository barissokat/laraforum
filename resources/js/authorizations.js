let user = window.App.user;

module.exports = {
    owns  (model, prob = 'user_id') {
        return model[prob] === user.id;
    }
};
