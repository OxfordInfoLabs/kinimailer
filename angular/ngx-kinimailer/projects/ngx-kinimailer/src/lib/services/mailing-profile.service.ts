import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient} from '@angular/common/http';

@Injectable({
    providedIn: 'root'
})
export class MailingProfileService {

    constructor(private config: KinimailerModuleConfig,
                private http: HttpClient) {
    }

    /**
     *
     * @param search
     * @param offset
     * @param limit
     * @param projectKey
     */
    public getMailingProfiles(search = '', offset = '0', limit = '10', projectKey = '') {
        return this.http.get(this.config.backendURL + '/mailingProfile', {
            params: {search, offset, limit}
        });
    }

    /**
     *
     * @param mailingProfileSummary
     * @param projectKey
     */
    public saveMailingProfile(mailingProfileSummary, projectKey = '') {
        return this.http.post(this.config.backendURL + '/mailingProfile?projectKey=' + projectKey,
            mailingProfileSummary).toPromise();
    }

    /**
     *
     * @param mailingProfileId
     */
    public removeMailingProfile(mailingProfileId) {
        return this.http.delete(this.config.backendURL + '/mailingProfile/' + mailingProfileId)
            .toPromise();
    }
}
