import axios from "axios";

export default {
    namespaced: true,
    state: () => ({
        user: null,
        roles: [],
        permissions: [],
        token: localStorage.getItem('auth_token') || ''
    }),
    getters: {
        isAuthenticated: state => !!state.token,
        hasRole: (state) => (role) => state.roles.includes(role),
        hasPermission: (state) => (permission) => state.permissions.includes(permission)
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user;
        },
        SET_ROLES(state, roles) {
            state.roles = roles;
        },
        SET_PERMISSIONS(state, permissions) {
            state.permissions = permissions;
        },
        SET_TOKEN(state, token) {
            state.token = token;
            localStorage.setItem('auth_token', token);
        },
        LOGOUT(state) {
            state.user = null;
            state.roles = [];
            state.permissions = [];
            state.token = '';
            localStorage.removeItem('auth_token');
        }
    },
    actions: {
        async fetchUser({ commit, state }) {
      try {
            const response = await axios.get('api/fetchUser', {
                headers: {
                    Authorization: `Bearer ${state.token}`,
                },
                });
                commit('SET_USER', response.data.user);
                commit('SET_ROLES', response.data.roles);
                commit('SET_PERMISSIONS', response.data.permissions);
            } catch (error) {
                console.error('Fetch User error', error);
            }
        },
        logout({commit}) {
            commit('LOGOUT');
        }
    }
};