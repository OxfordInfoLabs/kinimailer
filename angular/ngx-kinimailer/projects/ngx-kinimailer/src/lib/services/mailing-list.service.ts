import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';

@Injectable({
    providedIn: 'root'
})
export class MailingListService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient) {
    }

    public getMailingList(id: any) {
        return this.http.get(this.config.backendURL + '/mailingList/' + id).toPromise();
    }

    public filterMailingList(filterString = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/mailingList', {
            params: {filterString, offset, limit, projectKey}
        });
    }

    public getSubscribersForMailingList(mailingListId: number) {
        return this.http.get(this.config.backendURL + '/mailingList/subscribers', {
            params: {mailingListId}
        }).toPromise();
    }

    public isKeyAvailable(proposedKey: string): Promise<boolean> {
        return this.http.get(this.config.backendURL + '/mailingList/keyAvailable', {
            params: {proposedKey}
        }).toPromise().then((res: any) => {
            return !!res;
        });
    }

    public saveMailingList(mailingList: any, projectKey = '') {
        return this.http.post(this.config.backendURL + '/mailingList', mailingList)
            .toPromise();
    }
}
