import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';

@Injectable({
    providedIn: 'root'
})
export class TemplateService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient) {
    }

    public getTemplate(id) {
        return this.http.get(this.config.backendURL + '/template/' + id).toPromise();
    }

    public filterTemplates(filterString = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/template', {
            params: {filterString, offset, limit, projectKey}
        });
    }

    public saveTemplate(template: any, projectKey = '') {
        return this.http.post(this.config.backendURL + '/template', template)
            .toPromise();
    }

    public removeTemplate(id) {
        return this.http.delete(this.config.backendURL + '/template', {
            params: {templateId: id}
        }).toPromise();
    }

    public evaluateTemplate(template): Promise<string> {
        return this.http.post(this.config.backendURL + '/template/evaluate', template)
            .toPromise().then((res: string) => res);
    }
}
