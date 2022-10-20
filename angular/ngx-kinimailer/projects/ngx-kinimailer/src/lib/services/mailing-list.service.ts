import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';
import {MatSnackBar} from '@angular/material/snack-bar';

@Injectable({
    providedIn: 'root'
})
export class MailingListService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient,
                private snackBar: MatSnackBar) {
    }

    public getMailingList(id: any) {
        return this.http.get(this.config.backendURL + '/mailingList/' + id).toPromise();
    }

    public filterMailingList(filterString = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/mailingList', {
            params: {filterString, offset, limit, projectKey, accountId: this.config.accountId}
        });
    }

    public getSubscribersForMailingList(mailingListId: number) {
        return this.http.get(this.config.backendURL + '/mailingList/subscribers', {
            params: {mailingListId, accountId: this.config.accountId}
        }).toPromise();
    }

    public isKeyAvailable(proposedKey: string): Promise<boolean> {
        return this.http.get(this.config.backendURL + '/mailingList/keyAvailable', {
            params: {proposedKey, accountId: this.config.accountId}
        }).toPromise().then((res: any) => {
            return !!res;
        });
    }

    public saveMailingList(mailingList: any, projectKey = '') {
        return this.http.post(this.config.backendURL + '/mailingList', mailingList)
            .toPromise().then(res => {
                this.snackBar.open('Mailing Successfully Saved.', null,{
                    duration: 3000,
                    verticalPosition: 'top'
                });
                return res;
            });
    }
}
