import axios from "axios";
import setup from "../../setup";
import { mapState } from "pinia";
import { store } from "..";

async function execute() {
    let state = mapState(store, ['item']);

    let url = `${setup.api_host}/${setup.api_version}${setup.api_end_point}/update-status`;

    try {
        let response = await axios.post(url, {slug: state.item().slug, status: state.item().status });
        return response;
    } catch (error) {
        error.response;
    }
}

export default execute;