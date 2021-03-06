<?php

namespace app\models;

use app\commands\RbacController;
use app\filters\ClientUserLayout;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * BeaconsSearch represents the model behind the search form about `app\models\Beacons`.
 */
class BeaconsSearch extends Beacons
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'minor', 'major'], 'integer'],
            [['title', 'description', 'picture', 'place', 'uuid'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param null $user_id
     * @param null $group_id
     * @throws \yii\web\NotFoundHttpException
     * @return ActiveDataProvider
     */
    public function search($user_id = null, $group_id = null)
    {
        $query = Beacons::find();
        if(!Yii::$app->user->can(RbacController::admin))
        {
            $user = Users::getLogged(true);
            $user->getBeaconsQuery($query);
        }
        if($user_id !== null)
        {
            $user = Users::findOne(['id'=>$user_id]);
            $user->getBeaconsQuery($query);
        }
        if($group_id !== null)
        {
            $group = Groups::findOne(['id'=>$group_id]);
            if($group instanceof Groups)
            $query = $group->getBeacons();
        }



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }



        $query->andFilterWhere([
            'id' => $this->id,
            'minor' => $this->minor,
            'major' => $this->major,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'uuid', $this->uuid]);

        return $dataProvider;
    }



    public function clientBeacons($client_user_id = null) {
        $query = Beacons::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if($client_user_id !== null)
        {
            $user = ClientUsers::findOne(['id'=>$client_user_id]);
            $query->joinWith([
                                 'clientBeacons'=>function(ActiveQuery $query) use ($user)
                                 {

                                     $query->andFilterWhere(['client_id'=>$user->id]);

                                 }
                             ]);
        }



        $query->andFilterWhere([
                                   'id' => $this->id,
                                   'minor' => $this->minor,
                                   'major' => $this->major,
                               ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'uuid', $this->uuid]);

        return $dataProvider;
    }

}
