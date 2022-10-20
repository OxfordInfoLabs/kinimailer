import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';
import {MatSnackBar} from '@angular/material/snack-bar';

@Injectable({
    providedIn: 'root'
})
export class TemplateService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient,
                private snackBar: MatSnackBar) {
    }

    public getTemplate(id) {
        return this.http.get(this.config.backendURL + '/template/' + id).toPromise();
    }

    public filterTemplates(filterString = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/template', {
            params: {filterString, offset, limit, projectKey, accountId: this.config.accountId}
        });
    }

    public saveTemplate(template: any, projectKey = '') {
        return this.http.post(this.config.backendURL + '/template', template)
            .toPromise().then(res => {
                this.snackBar.open('Mailing Successfully Saved.', null,{
                    duration: 3000,
                    verticalPosition: 'top'
                });
                return res;
            });
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
