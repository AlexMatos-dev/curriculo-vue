import SwaggerUI from 'swagger-ui'
import 'swagger-ui/dist/swagger-ui.css';

function getHostName(){
    return location.protocol + '//' + location.hostname + '/api.yaml';
}

SwaggerUI({
    dom_id: '#swagger-api',
    url: getHostName(),
});

