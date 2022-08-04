import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';
import {MatSnackBar} from '@angular/material/snack-bar';

@Injectable({
    providedIn: 'root'
})
export class MailingService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient,
                private snackBar: MatSnackBar) {
    }

    public getMailing(id) {
        return this.http.get(this.config.backendURL + '/mailing/' + id).toPromise();
    }

    public filterMailings(filterString = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/mailing', {
            params: {filterString, offset, limit, projectKey}
        });
    }

    public saveMailing(mailing: any, projectKey = '') {
        return this.http.post(this.config.backendURL + '/mailing', mailing)
            .toPromise().then(res => {
                this.snackBar.open('Mailing Successfully Saved.', null,{
                    duration: 3000,
                    verticalPosition: 'top'
                });
                return res;
            });
    }

    public removeMailing(id) {
        return this.http.delete(this.config.backendURL + '/mailing/' + id).toPromise();
    }
}
