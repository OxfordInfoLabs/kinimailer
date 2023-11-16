import {Injectable} from '@angular/core';
import {KinimailerModuleConfig} from '../ngx-kinimailer.module';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {MatSnackBar} from '@angular/material/snack-bar';
import {map, switchMap} from 'rxjs/operators';
import {interval} from 'rxjs';

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
            params: {filterString, offset, limit, projectKey, accountId: this.config.accountId}
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

    public sendMailing(mailingId, trackingKey, projectKey = '', sendNow = false) {
        const url = '/mailing/send?trackingKey=' + trackingKey + '&projectKey=' + projectKey + '&sendNow=' + sendNow;
        return this.http.post(this.config.backendURL + url,
            mailingId)
            .toPromise();
    }

    public getDataTrackingResults(trackingKey) {
        return this.http.get(this.config.backendURL + `/mailing/results/${trackingKey}`).toPromise();
    }

    public loadDataTrackingResults(trackingKey) {
        return interval(2000)
            .pipe(
                switchMap(() =>
                    this.http.get(this.config.backendURL + `/mailing/results/${trackingKey}`).pipe(
                        map(result => {
                            return result;
                        }))
                )
            );
    }

    public sendMailingTest(name, emailAddress, mailing, fromAddress, replyToAddress, ccAddresses = '', bccAddresses = '') {
        return this.http.post(this.config.backendURL + '/mailing/sendAdhoc', {
            mailingId: mailing.id, name, emailAddress, sections: mailing.templateSections, parameters: mailing.templateParameters, title: mailing.title, fromAddress, replyToAddress, ccAddresses, bccAddresses
        }).toPromise();
    }

    public uploadAttachments(mailingId: number, fileUploads: any) {
        const HttpUploadOptions = {
            headers: new HttpHeaders({'Content-Type': 'file'})
        };
        return this.http.post(this.config.backendURL + '/mailing/attachments/' + mailingId,
            fileUploads, HttpUploadOptions)
            .toPromise();
    }

    public removeAttachment(mailingId: number, attachmentId: number) {
        return this.http.delete(this.config.backendURL + `/mailing/attachments/${mailingId}/${attachmentId}`)
            .toPromise();
    }

    public streamAttachment(id: number) {
        window.open(this.config.backendURL + '/attachment/' + id);
    }
}
