import { boot } from "quasar/wrappers";
import axios from "axios";
import { useAuthStore } from "src/stores/auth";

const BASE_URL = process.env?.VUE_APP_API_BASE_URL || null;
const ENDPOINT = BASE_URL ? `${BASE_URL}/api/v1/settings/get` : null;


let settings = {
    data: null,
    getSettings: async function (){
        const auth_store = useAuthStore()
        if(!auth_store.getToken){
            return false;
        }
        
        if(ENDPOINT){
            const {data, status} = await axios({
                method: 'GET',
                url:ENDPOINT,
                headers: {
                    Authorization: `Bearer ${auth_store.getToken}`,
                },
            });
        
            if([200, 201].includes(status)){
                // update the settings.data
                this.data = data.data.settings?.data || null;
            }
        }
    }
};

export default boot(async ({ app }) => {
    app.config.globalProperties.$settings = settings; 
});