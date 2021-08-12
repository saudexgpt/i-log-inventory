import Resource from '@/api/resource';

const state = {
};

const mutations = {
};

const actions = {
  needToSubscribe() {
    const checkSubscriptionResource = new Resource('need-to-subscribe');
    return new Promise((resolve, reject) => {
      checkSubscriptionResource.list()
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject('Error: ' + error);
        });
    });
  },

  fetchSubscription() {
    const fetchSubscriptionResource = new Resource('fetch-subscription');
    return new Promise((resolve, reject) => {
      fetchSubscriptionResource.list()
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject('Error: ' + error);
        });
    });
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
